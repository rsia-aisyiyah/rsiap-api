<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $payload = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->payload(); // <- get payload from token
            $request->merge(['payload' => $payload]); // <- this code can be used to get payload from request
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return isUnauthenticated('Token is Invalid');
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return isUnauthenticated('Token is Expired');
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
                return isUnauthenticated('There is problem with your token');
            } else {
                return isUnauthenticated('Authorization Token not found');
            }
        }
        return $next($request);

    }
}