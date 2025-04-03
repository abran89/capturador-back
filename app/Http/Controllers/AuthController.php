<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->estado) {
            return response()->json(['error' => 'Usuario deshabilitado'], 403);
        }

        return response()->json([
        'token' => $token,
        'password_changed' => $user->password_changed
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function loginWeb(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withInput()->with('error', 'Credenciales incorrectas.');
        }

        $remember = $request->has('remember');

        // Crear token JWT
        $token = JWTAuth::fromUser($user);

        // Guardar el token en la sesión para mantener la autenticación del usuario
        session(['jwt_token' => $token]);

        // Si el usuario quiere ser recordado
        if ($remember) {
            // Crear cookie con el token, durará 1 año (525600 minutos)
            $cookie = cookie('remember_email', $request->email, 525600);
            // Redirigir al dashboard con la cookie
            return redirect()->route('admin.dashboard')->cookie($cookie);
        }

        // Si no hay "Recordarme", solo redirigir al dashboard
        return redirect()->route('admin.dashboard');
    }

    public function logoutWeb(Request $request)
    {
        try {
            // Intenta obtener el token desde la sesión
            $token = session('jwt_token');

            if ($token) {
                // Establece el token en el JWTAuth
                JWTAuth::setToken($token);
                // Invalidar el token
                JWTAuth::invalidate($token);
            }

            // Eliminar el JWT de la sesión
            $request->session()->forget('jwt_token');

            // Redirigir al login
            return redirect()->route('login')->with('message', 'Has cerrado sesión correctamente.');
        } catch (JWTException $e) {
            // En caso de que haya un error con el token
            return redirect()->route('login')->with('error', 'No se pudo invalidar el token.');
        }
    }
}
