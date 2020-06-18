<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function balance(User $user)
    {
        $balance = User::totalBalance($user->id);

        return [
            'data' => [
                'balance' => $balance,
            ]
        ];
    }
}
