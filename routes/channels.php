<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.global', function ($user) {
    // Anyone authenticated can listen to global chat
    return (bool) $user;
});


