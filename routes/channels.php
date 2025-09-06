<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Simple presence channel for online users
Broadcast::channel('presence.users', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});

// Private user channel
Broadcast::channel('App.Models.User.{id}', function ($user, int $id) {
    return (int) $user->id === (int) $id;
});

// Location-scoped channel, any authenticated user with active location may listen
Broadcast::channel('location.{locationId}', function ($user, int $locationId) {
    // If you have explicit permissions per location, enforce them here
    // For now allow authenticated users; tighten later if needed
    return ! is_null($user);
});
