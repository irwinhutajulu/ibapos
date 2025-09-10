<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActiveLocationController extends Controller
{
    public function set(Request $request)
    {
        $request->validate(['location_id' => 'required|integer']);
        $activeId = (int)$request->integer('location_id');
        session(['active_location_id' => $activeId]);

        // Return JSON for AJAX clients to avoid redirects
        if ($request->wantsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'success' => true,
                'active_location_id' => $activeId,
            ]);
        }

        return back();
    }
}
