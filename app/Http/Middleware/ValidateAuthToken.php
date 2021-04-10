<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
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
            return response()->json([
                'error' => 'Invalid Access Token'
            ], 403);
        }
        return $next($request);
    }

    protected function validateAuthToken($request) {
        $header = $request->header('Authorization', '');
        if (Str::startsWith($header, 'Bearer ')) {
            $token = explode('Bearer ', $header)[1];
            $userCount = User::where('api_token', '=', $token)->count();
            return $userCount > 0;
        }
        return false;
    }
}
