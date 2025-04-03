<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Models\User;


class UserController extends Controller
{
    public function profile(Request $request)
    {
        return response()->json([
            'user' => Auth::user()
        ], 200);
    }

    public function index()
    {
        $usuarios = User::where('role', 'user')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users'
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('todocarnes'),
                'estado' => true
            ]);

            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hubo un error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->estado = !$usuario->estado;
        $usuario->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Estado del usuario actualizado.');
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');

        } catch (QueryException $e) {
            return redirect()->route('admin.usuarios.index')->with('error', 'No se puede eliminar el usuario porque tiene dependencias asociadas.');
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!$user instanceof User) {
            return response()->json(['success' => false, 'message' => 'Usuario no válido.'], 400);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'La contraseña actual es incorrecta.'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->password_changed = true;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Contraseña cambiada con éxito.']);
    }
}
