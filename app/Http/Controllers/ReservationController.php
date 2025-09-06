<?php

namespace App\Http\Controllers;

use App\Models\StockReservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $query = DB::table('stock_reservations as r')
            ->join('products as p','p.id','=','r.product_id')
            ->join('locations as l','l.id','=','r.location_id')
            ->select('r.*','p.name as product_name','l.name as location_name')
            ->orderByDesc('r.id');
        if ($status) {
            $query->where('r.status', $status);
        }
        $reservations = $query->paginate(20)->withQueryString();
        return view('reservations.index', compact('reservations','status'));
    }

    public function release(StockReservation $reservation, ReservationService $service)
    {
        $service->release($reservation, auth()->id());
        return back()->with('ok','Reservation released');
    }

    public function consume(StockReservation $reservation, ReservationService $service)
    {
        $service->consume($reservation, auth()->id());
        return back()->with('ok','Reservation consumed');
    }

    public function cleanupExpired(ReservationService $service)
    {
        $count = $service->cleanupExpired();
        return back()->with('ok', "Expired reservations marked: {$count}");
    }
}
