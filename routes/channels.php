<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('chat.{userA}.{userB}', function (User $user, $userA, $userB) {
    return (int)$user->id === (int)$userA || (int)$user->id === (int)$userB;
});

Broadcast::channel('user.{id}', function (User $user, $id) {
    return (int)$user->id === (int)$id;
});