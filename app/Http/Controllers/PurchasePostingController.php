<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Services\PurchasePostingService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PurchasePostingController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $status = $request->get('status');
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');

        $base = Purchase::query()
            ->when($q, fn($b)=>$b->where(function($x) use($q){
                $x->where('invoice_no','like',"%$q%")
                  ->orWhereHas('supplier', fn($s)=>$s->where('name','like',"%$q%"));
            }))
            ->when($status, fn($b)=>$b->where('status',$status))
            ->when($dateFrom, fn($b)=>$b->whereDate('date','>=',$dateFrom))
            ->when($dateTo, fn($b)=>$b->whereDate('date','<=',$dateTo));

        if ($request->wantsJson()) {
            $json = (clone $base)->with('supplier')->latest('date')->paginate(20);
            return response()->json($json);
        }

        $purchases = (clone $base)->with(['supplier:id,name'])
            ->latest('date')
            ->paginate(20)
            ->withQueryString();
        return view('purchases.index', compact('purchases','q','status','dateFrom','dateTo'));
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['items.product:id,name,barcode,unit','supplier:id,name','user:id,name']);
        return view('purchases.show', compact('purchase'));
    }
    // Route-level middleware is applied in routes/web.php

    public function receive(Request $request, Purchase $purchase, PurchasePostingService $service)
    {
        $this->authorize('receive', $purchase);
        $service->markAsReceived($purchase, (int)auth()->id());
        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'purchase' => $purchase->fresh('items')]);
        }
        return redirect()->back()->with('ok', 'Purchase marked as received.');
    }

    public function post(Request $request, Purchase $purchase, PurchasePostingService $service)
    {
        $this->authorize('post', $purchase);
        $service->post($purchase, (int)auth()->id());
        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'purchase' => $purchase->fresh('items')]);
        }
        return redirect()->back()->with('ok', 'Purchase posted.');
    }

    public function void(Request $request, Purchase $purchase, PurchasePostingService $service)
    {
        $this->authorize('void', $purchase);
        $service->void($purchase, (int)auth()->id());
        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'purchase' => $purchase->fresh('items')]);
        }
        return redirect()->back()->with('ok', 'Purchase voided.');
    }
}
