<?php

namespace App\Http\Controllers;

use App\Models\StockMutation;
use App\Services\MutationService;
use Illuminate\Http\Request;

class StockMutationsController extends Controller
{
    public function index()
    {
        $mutations = \App\Models\StockMutation::latest('date')->paginate(20);
        return view('mutations.index', compact('mutations'));
    }
    // Route-level middleware is applied in routes/web.php

    public function confirm(Request $request, StockMutation $mutation, MutationService $service)
    {
        $service->confirm($mutation, (int)auth()->id());
        return response()->json(['status' => 'ok', 'mutation' => $mutation->fresh()]);
    }

    public function reject(Request $request, StockMutation $mutation, MutationService $service)
    {
        $service->reject($mutation, (int)auth()->id(), $request->string('reason')->toString() ?: null);
        return response()->json(['status' => 'ok', 'mutation' => $mutation->fresh()]);
    }
}
