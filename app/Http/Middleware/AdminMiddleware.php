<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
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
        if(auth('api')->check()){
            if (auth('api')->user()->role_id == 0) {
                return $next($request);
            }
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => 'Unauthorized. You are not Admin'
                ],
                401);
        }
        return response()->json(
            ['msg' => 'Please log in as Admin'],
            401);


    }
}
