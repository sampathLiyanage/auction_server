<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use App\User;
use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ValidateAuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->validateAuthToken($request)) {
            throw new UnauthorizedException('Invalid API Token');
        }
        return $next($request);
    }

    protected function validateAuthToken($request) {
        $header = $request->header('Authorization', '');
        if (Str::startsWith($header, 'Bearer ')) {
            $token = explode('Bearer ', $header)[1];
            $user = User::where('api_token', '=', $token)->first();
            if (!is_null($user)) {
                Session::put('loggedInUserId', $user->id);
                return true;
            }
        }
        return false;
    }
}
