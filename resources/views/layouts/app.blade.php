<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <style>
        body {
            background: linear-gradient(45deg, #1e1e1e, #343a40);
            color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            min-height: 100vh;  /* Para asegurar que el footer esté al final de la página */
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #1f1f1f;
            padding: 10px 0;  /* Reducir el padding para que el header sea más delgado */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;  /* Asegura que el header ocupe todo el ancho */
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.5rem;  /* Reducir el tamaño de la fuente para que sea más sutil */
            font-weight: bold;
            color: #fff;
        }

        .navbar-nav .nav-link {
            color: #fff !important;
        }

        .navbar-nav .nav-link:hover {
            color: #f39c12 !important;
        }

        .content {
            background-color: #1f1f1f;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
            margin-top: 90px;  /* Aumentar el margen superior para no solaparse con el header */
            flex-grow: 1;  /* Asegura que el contenido ocupe todo el espacio disponible */
        }

        footer {
            background-color: #1f1f1f;
            padding: 15px;
            text-align: center;
            color: #777;
            width: 100%; /* Asegura que el footer ocupe todo el ancho */
            position: fixed;
            bottom: 0;
            left: 0;
        }

        .card {
            background-color: #343a40;
            border: none;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            background-color: #444;
            color: #fff;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .form-control, .btn {
            background-color: #333;
            border: 1px solid #555;
            color: #fff;
            transition: border-color 0.3s ease;
        }

        .form-control:focus, .btn:focus {
            box-shadow: none;
            border-color: #f39c12;
        }

        .btn-primary {
            background-color: #f39c12;
            border-color: #f39c12;
        }

        .btn-primary:hover {
            background-color: #e67e22;
            border-color: #e67e22;
        }

        .alert-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Mostrar el menú solo si no estamos en la ruta de login y el usuario está autenticado -->
        @auth
            <header>
                <nav class="navbar navbar-expand-lg navbar-dark">
                    <div class="container">
                        <a class="navbar-brand" href="#">Panel de administrador</a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.usuarios.index') }}">Usuarios</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.ordenes.index') }}">Ordenes</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('logout') }}">Cerrar sesión</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>
        @endauth

        <!-- Contenido -->
        <div class="content">
            @yield('content')
        </div>

    </div>

    <footer>
        &copy; {{ date('Y') }} Laravel 10 - AdminPanel. Todos los derechos reservados.
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
