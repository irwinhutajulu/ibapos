<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SalesController extends Controller
{
    // Route-level middleware is applied in routes/web.php

    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $status = $request->get('status');
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');

        $base = Sale::query()
            ->when($q, fn($b)=>$b->where(function($x) use($q){
                $x->where('invoice_no','like',"%$q%")
                  ->orWhereHas('customer', fn($c)=>$c->where('name','like',"%$q%"));
            }))
            ->when($status, fn($b)=>$b->where('status',$status))
            ->when($dateFrom, fn($b)=>$b->whereDate('date','>=',$dateFrom))
            ->when($dateTo, fn($b)=>$b->whereDate('date','<=',$dateTo));

        // JSON for tests or API consumers
        if (app()->runningUnitTests() || $request->wantsJson()) {
            $json = (clone $base)->with('items')->latest('date')->paginate(20);
            return response()->json($json);
        }

        $sales = (clone $base)->with(['user:id,name','customer:id,name'])
            ->latest('date')
            ->paginate(20)
            ->withQueryString();
        return view('sales.index', compact('sales','q','status','dateFrom','dateTo'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product:id,name,barcode,unit','payments','user:id,name','customer:id,name']);
        
        // JSON for tests or API consumers
        if (app()->runningUnitTests() || request()->wantsJson()) {
            return response()->json($sale);
        }
        
        return view('sales.show', compact('sale'));
    }

    public function store(Request $request, ReservationService $reservations)
    {
        $data = $request->validate([
            'invoice_no' => 'required|string|max:50',
            'date' => 'required|date',
            'customer_id' => 'nullable|integer',
            'additional_fee' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'payment' => 'nullable|numeric',
            'change' => 'nullable|numeric',
            'payment_type' => 'nullable|string|max:30',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.qty' => 'required|numeric',
            'items.*.price' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
            'items.*.subtotal' => 'required|numeric',
            'items.*.source_location_id' => 'nullable|integer',
            'reserve' => 'nullable|boolean',
            'payments' => 'nullable|array',
            'payments.*.type' => 'required_with:payments|string|max:30',
            'payments.*.amount' => 'required_with:payments|numeric|min:0',
            'payments.*.reference' => 'nullable|string|max:50',
            'payments.*.note' => 'nullable|string|max:255',
        ]);

    $locationId = (int) session('active_location_id');

        // Enforce permission for remote stock usage
        $usesRemote = collect($data['items'])->contains(fn($it) => !empty($it['source_location_id']) && (int)$it['source_location_id'] !== $locationId);
        if ($usesRemote && !auth()->user()->can('sales.use_remote_stock')) {
            throw new AccessDeniedHttpException('No permission: sales.use_remote_stock');
        }

        // Server-side stock availability validation
        foreach ($data['items'] as $row) {
            $pid = (int)$row['product_id'];
            $lid = (int)($row['source_location_id'] ?? $locationId);
            $onHand = (float) \App\Models\Stock::where('product_id',$pid)->where('location_id',$lid)->value('qty') ?? 0.0;
            $reserved = (float) \App\Models\StockReservation::where('product_id',$pid)->where('location_id',$lid)->where('status','active')->sum('qty_reserved');
            $available = $onHand - $reserved;
            if ($available < (float)$row['qty']) {
                return response()->json(['message' => 'Insufficient stock for product '.$pid.' at location '.$lid, 'available' => $available], 422);
            }
        }

        $sale = DB::transaction(function () use ($data, $locationId, $reservations) {
            $subtotal = collect($data['items'])->reduce(function ($acc, $it) {
                return $acc + (float) ($it['subtotal'] ?? 0);
            }, 0.0);
            $additionalFee = (float)($data['additional_fee'] ?? 0);
            $discount = (float)($data['discount'] ?? 0);
            $total = max(0, $subtotal + $additionalFee - $discount);

            // Ensure invoice_no exists: generate if missing
            $invoiceNo = $data['invoice_no'] ?? null;
            if (empty($invoiceNo)) {
                $invoiceNo = \App\Services\InvoiceGenerator::next('sale', $locationId);
            }

            $sale = Sale::create([
                'invoice_no' => $invoiceNo,
                'date' => $data['date'],
                'user_id' => auth()->id(),
                'location_id' => $locationId,
                'customer_id' => $data['customer_id'] ?? null,
                'additional_fee' => $additionalFee,
                'discount' => $discount,
                'total' => $total,
                'payment' => 0,
                'change' => 0,
                'payment_type' => $data['payment_type'] ?? null,
                'status' => 'draft',
            ]);

            $items = [];
            foreach ($data['items'] as $row) {
                $items[] = new SaleItem([
                    'product_id' => $row['product_id'],
                    'qty' => $row['qty'],
                    'price' => $row['price'],
                    'discount' => $row['discount'] ?? 0,
                    'subtotal' => $row['subtotal'],
                    'source_location_id' => $row['source_location_id'] ?? null,
                ]);
            }
            $sale->items()->saveMany($items);

            // payments
            if (!empty($data['payments'])) {
                $sum = 0.0;
                foreach ($data['payments'] as $pay) {
                    $sale->payments()->create([
                        'type' => $pay['type'],
                        'amount' => $pay['amount'],
                        'reference' => $pay['reference'] ?? null,
                        'note' => $pay['note'] ?? null,
                        'paid_at' => now(),
                    ]);
                    $sum += (float)$pay['amount'];
                }
                // Recalculate aggregated payment fields from saved payments
                $sale->recalculatePayments();
            }

            if (!empty($data['reserve'])) {
                foreach ($sale->items as $item) {
                    $src = $item->source_location_id ?: $locationId;
                    $reservations->reserve(
                        productId: $item->product_id,
                        locationId: $src,
                        qtyReserved: (string)$item->qty,
                        saleId: $sale->id,
                        saleItemId: $item->id,
                        userId: (int)auth()->id()
                    );
                }
            }

            return $sale;
        });

        return response()->json(['status' => 'ok', 'sale' => $sale->load('items')]);
    }
}
