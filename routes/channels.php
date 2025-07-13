<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('appointments', function ($user) {
    return $user->can('view_appointments') || $user->can('add_appointments') || $user->can('manage_appointments') || $user->can('appointment_submit');
});
