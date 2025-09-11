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

    // Tandai semua notifikasi user sebagai sudah dibaca
    public function markAllRead()
    {
        $user = auth()->user();
        \App\Models\Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return back()->with('ok', 'All notifications marked as read');
    }

    // Tampilkan preferensi notifikasi user
    public function preferences()
    {
        $user = auth()->user();
        $settings = \App\Models\NotificationSetting::where('user_id', $user->id)->get();
        $channels = ['email', 'webpush', 'inapp'];
        $types = ['sale_created', 'purchase_created', 'low_stock', 'stock_out']; // Bisa diambil dari config/event
        return view('admin.notifications.preferences', compact('settings', 'channels', 'types'));
    }

    // Update preferensi notifikasi user
    public function updatePreferences(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $channels = $request->input('channel', []);
        $types = $request->input('type', []);
        $enabled = $request->input('enabled', []);

        foreach ($channels as $channel) {
            foreach ($types as $type) {
                $setting = \App\Models\NotificationSetting::firstOrNew([
                    'user_id' => $user->id,
                    'channel' => $channel,
                    'type' => $type,
                ]);
                $setting->enabled = isset($enabled[$channel][$type]) ? (bool)$enabled[$channel][$type] : false;
                $setting->save();
            }
        }
        return back()->with('ok', 'Notification preferences updated');
    }
}
