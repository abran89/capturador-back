<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class JWTAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Intenta autenticar el token JWT
            JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            // Si no se puede autenticar, devuelve una respuesta 401
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
