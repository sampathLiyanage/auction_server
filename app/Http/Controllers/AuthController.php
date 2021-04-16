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
    /**
     * @api {post} api/login Login to System
     * @apiName Login
     * @apiGroup Auction
     *
     * @apiParam {String} username user name of the user
     * @apiParam {String} password password of the user
     *
     * @apiSuccess {Json} User data
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     * {
     *    "data": {
     *      "id":"1",
     *      "name":"user1",
     *      "api_token":"LQ7fI13n3GIazTslIH0Z4R2tT78QmJbX8Nd1J8355ZMYoSHZxGvIkiSY4ds0",
     *      "created_at":"2021-04-15T23:09:59.000000Z",
     *      "updated_at":"2021-04-15T23:09:59.000000Z",
     *    }
     * }
     *
     * @apiError Unauthorized Login Credentials Mismatch
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 403 Unauthorized
     *     {
     *       "message": "Login Credentials Mismatch"
     *     }
     */
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
