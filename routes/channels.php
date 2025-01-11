<?php

use App\Models\Account;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('avito.{accountId}.message', function ($user, $id) {
    return Account::relatedToMe()->whereId($id)->exists();
});
