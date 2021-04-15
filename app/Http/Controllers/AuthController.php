<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\UnauthorizedException;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request) {
        $credentials = $request->only('username', 'password');
        $validator = Validator::make($credentials,[
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->toJson());
        }
        $user = User::where([
            ['name', '=', $credentials['username']],
            ['password', '=', md5($credentials['password'])]
        ])->first();
        if (!is_null($user)) {
            $user->api_token = Str::random(60);
            $user->save();
            return response()->json([
                'data' => $user->toArray(),
            ], 200);
        }
        throw new UnauthorizedException('Login Credentials Mismatch');
    }

    public function logout(Request $request) {
        $token = request('token');
        $validator = Validator::make(['token'=>$token],[
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->toJson());
        }
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
        throw new BadRequestException('Invalid Token');
    }
}
