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
        $kasbon = Kasbon::create($validated);

        // Kirim notifikasi ke admin/finance
        $admins = \App\Models\User::role(['admin','finance'])->get();
        foreach ($admins as $admin) {
            app(\App\Services\NotificationService::class)->sendToUser(
                $admin,
                'kasbon_created',
                [
                    'kasbon_id' => $kasbon->id,
                    'user_id' => $kasbon->user_id,
                    'amount' => $kasbon->amount,
                    'note' => $kasbon->note,
                ]
            );
        }

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

        // Jika status berubah menjadi approved/rejected, kirim notifikasi ke user pembuat
        if (in_array($validated['status'], ['approved','rejected'])) {
            $creator = $kasbon->user;
            if ($creator) {
                $type = $validated['status'] === 'approved' ? 'kasbon_approved' : 'kasbon_rejected';
                app(\App\Services\NotificationService::class)->sendToUser(
                    $creator,
                    $type,
                    [
                        'kasbon_id' => $kasbon->id,
                        'status' => $validated['status'],
                        'amount' => $kasbon->amount,
                        'note' => $kasbon->note,
                    ]
                );
            }
        }

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
