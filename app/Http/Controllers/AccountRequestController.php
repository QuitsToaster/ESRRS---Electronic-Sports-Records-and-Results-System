<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountRequest;

class AccountRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:forgot_password,new_account',
        ]);

        // Create request in database
        AccountRequest::create([
            'email' => $request->email,
            'type' => $request->type,
            'message' => $request->message ?? null,
        ]);

        return response()->json([
            'message' => $request->type === 'forgot_password'
                ? 'Your password reset request has been sent to the admin.'
                : 'Your account request has been sent to the admin.'
        ]);
    }
}
