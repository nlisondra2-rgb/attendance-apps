<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $adminEmail = (string) env('ADMIN_EMAIL', '');
        $adminPassword = (string) env('ADMIN_PASSWORD', '');
        $adminPasswordHash = (string) env('ADMIN_PASSWORD_HASH', '');

        $emailOk = $adminEmail !== '' && hash_equals($adminEmail, $validated['email']);

        $passwordOk = false;
        if ($adminPasswordHash !== '') {
            $passwordOk = Hash::check($validated['password'], $adminPasswordHash);
        } elseif ($adminPassword !== '') {
            $passwordOk = hash_equals($adminPassword, $validated['password']);
        }

        if (!$emailOk || !$passwordOk) {
            return response()->json(['message' => 'Invalid admin credentials.'], 401);
        }

        return response()->json(['ok' => true]);
    }
}
