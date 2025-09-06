<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Support\Str;

class NotificationService
{
    public function sendToUser(User $user, string $type, array $data): Notification
    {
        // check settings (in-app only for now)
        $enabled = NotificationSetting::where('user_id',$user->id)->where('channel','inapp')->where('type',$type)->value('enabled');
        if ($enabled === 0) {
            // still store, but could skip in real impl
        }
        return Notification::create([
            'id' => (string) Str::uuid(),
            'type' => $type,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode($data),
        ]);
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->update(['read_at' => now()]);
    }
}
