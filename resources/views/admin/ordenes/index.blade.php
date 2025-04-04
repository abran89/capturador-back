@extends('layouts.app')

@section('content')
<div class="container text-white">
    <h4 class="mb-4">Ordenes de Compra</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-dark table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Código de orden</th>
                <th>Proveedor</th>
                <th>Usuario</th>
                <th>Estado</th>
                <th>Fecha creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordenes as $orden)
                <tr>
                    <td>{{ $orden->id }}</td>
                    <td>{{ $orden->numero_orden }}</td>
                    <td>{{ $orden->proveedor->rut }}</td>
                    <td>{{ $orden->usuario->name }}</td>
                    <td>
                        @if($orden->estado == "Pendiente")
                            <span class="badge bg-primary">{{$orden->estado}}</span>
                        @elseif($orden->estado == "Completa")
                            <span class="badge bg-success">{{$orden->estado}}</span>
                        @elseif($orden->estado == "Enviada completa")
                            <span class="badge bg-success">{{$orden->estado}}</span>
                        @elseif($orden->estado == "Enviada incompleta")
                            <span class="badge bg-danger">{{$orden->estado}}</span>
                        @endif
                    </td>
                    <td>{{ $orden->created_at }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="cargarProductos({{ $orden->id }})" data-bs-toggle="modal" data-bs-target="#productosModal">Productos</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $ordenes->links() }}
</div>

<!-- Modal -->
<div class="modal fade" id="productosModal" tabindex="-1" aria-labelledby="productosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
       <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="productosModalLabel">Productos de la Orden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow: auto;">
                <table class="table table-dark table-bordered">
                    <thead>
                        <tr>
                            <th>Código producto</th>
                            <th>Cantidad cajas</th>
                            <th>Valor unitario</th>
                            <th>Estado</th>
                            <th>Cajas ingresadas</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody id="productosTableBody">
                        <tr>
                            <td colspan="3" class="text-center">Cargando...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function cargarProductos(ordenId) {
        let tableBody = document.getElementById("productosTableBody");
        tableBody.innerHTML = "<tr><td colspan='3' class='text-center'>Cargando...</td></tr>";

        fetch(`/ordenes/${ordenId}/productos`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
        }).then(response => {
            if (response.status === 401) {
                window.location.href = '/login';
                throw new Error('Token inválido');
            }

         return response.json();

        }).then(data => {
                tableBody.innerHTML = "";

                if (data.length === 0) {
                    tableBody.innerHTML = "<tr><td colspan='3' class='text-center'>No hay productos</td></tr>";
                } else {
                    data.forEach(producto => {
                        let cantidad = producto.productos_ingresados?.[0]?.cantidad_cajas || '';
                        let usuario = producto.productos_ingresados?.[0]?.usuario.name || '';

                        let estado = null;
                        if(producto.estado == 'Pendiente'){
                            estado = '<span class="badge bg-primary">Pendiente</span>'
                        } else if(producto.estado == 'Ingresado'){
                             estado = '<span class="badge bg-success">Ingresado</span>'
                        } else if(producto.estado == 'Modificado'){
                             estado = '<span class="badge bg-danger">Modificado</span>'
                        }
                        else {
                            estado = '<span class="badge bg-warning">Nuevo</span>'
                        }

                        let row = `<tr>
                            <td>${producto.codigo_producto}</td>
                            <td>${producto.cantidad_cajas}</td>
                            <td>${producto.valor_unitario}</td>
                            <td>${estado}</td>
                            <td>${cantidad}</td>
                            <td>${usuario}</td>
                        </tr>`;
                        tableBody.innerHTML += row;
                    });
                }
            })
            .catch(error => {
                console.error("Error al cargar los productos:", error);
                tableBody.innerHTML = "<tr><td colspan='3' class='text-center text-danger'>Error al cargar los productos</td></tr>";
            });
    }
</script>
@endsection
