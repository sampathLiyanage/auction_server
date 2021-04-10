<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request) {
        $credentials = $request->only('username', 'password');
        $user = User::where([
            ['name', '=', $credentials['username']],
            ['password', '=', md5($credentials['password'])]
        ])->first();
        if (!is_null($user)) {
            $user->api_token = Str::random(60);
            $user->save();
            return response()->json([
                'data' => $user->toArray(),
            ]);
        }
        return response()->json([
            'error' => 'Login Credentials Mismatch'
        ], 401);
    }

    public function logout(Request $request) {
        $token = request('token', false);
        if ($token) {
            $user = User::where('api_token', '=', $token)->first();
            if (!is_null($user)) {
                $user->api_token = null;
                $user->save();
                return response()->json([
                    'message' => 'Logged Out Successfully'
                ], 200);
            }
        }
        return response()->json([
            'error' => 'Invalid token'
        ], 400);
    }
}
