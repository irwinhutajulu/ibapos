<?php
namespace App\Http\Controllers;

use App\Models\Kasbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasbonController extends Controller
{
    public function index()
    {
        $kasbons = Kasbon::with(['requester','approver','location'])->latest()->paginate(20);
        return view('kasbons.index', compact('kasbons'));
    }

    public function create()
    {
        return view('kasbons.partials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'location_id' => 'required|exists:locations,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);
        $validated['status'] = 'pending';
        Kasbon::create($validated);
        return redirect()->route('kasbons.index')->with('success','Kasbon created');
    }

    public function edit(Kasbon $kasbon)
    {
        return view('kasbons.partials.edit', compact('kasbon'));
    }

    public function update(Request $request, Kasbon $kasbon)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);
        $kasbon->update($validated);
        return redirect()->route('kasbons.index')->with('success','Kasbon updated');
    }

    public function show(Kasbon $kasbon)
    {
        return view('kasbons.partials.show', compact('kasbon'));
    }

    public function destroy(Kasbon $kasbon)
    {
        $kasbon->delete();
        return redirect()->route('kasbons.index')->with('success','Kasbon deleted');
    }
}
