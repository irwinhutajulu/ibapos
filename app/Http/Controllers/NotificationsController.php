<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $notifications = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }
        return back()->with('ok','Notification marked as read');
    }
}
