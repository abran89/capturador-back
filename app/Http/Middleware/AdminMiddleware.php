<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = session('jwt_token');

            if (!$token) {
                return redirect()->route('login')->with('error', 'Token no encontrado');
            }

            // Intentamos autenticar al usuario con el token desde la sesión
            $user = JWTAuth::setToken($token)->authenticate();

            if ($user && $user->role === 'admin') {
                return $next($request);
            }

            return redirect()->route('login')->withInput()->with('error', 'Acceso denegado. No eres administrador.');

        } catch (\Exception $e) {
            // Si el token no es válido, redirigimos con un error
            return redirect()->route('login')->withInput()->with('error', 'Token no válido o expirado.');
        }
    }
}
