<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActiveLocationController extends Controller
{
    public function set(Request $request)
    {
        $request->validate(['location_id' => 'required|integer']);
        session(['active_location_id' => (int)$request->integer('location_id')]);
        return back();
    }
}
