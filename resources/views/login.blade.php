@extends('layouts.app')

@section('title', 'Login de Administrador')

@section('content')
    <div class="container d-flex justify-content-center align-items-center">
        <div class="w-100" style="max-width: 400px;">
            <h2 class="text-center mb-4">Login de Administrador</h2>

            @if(session('error'))
                <div class="alert alert-danger text-white">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', request()->cookie('remember_email') ? Cookie::get('remember_email') : '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña:</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="8" required>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember"  {{ old('remember') || request()->cookie('remember_email') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Recordarme</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </form>
        </div>
    </div>
@endsection
