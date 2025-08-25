<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Productos Disponibles</h2>
            <div class="d-flex">
                <div class="input-group me-2" style="width: 300px;">
                    <input type="text" id="search-input" class="form-control" placeholder="Buscar productos...">
                    <button class="btn btn-outline-secondary" type="button" onclick="buscarProductos()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <button class="btn btn-primary" onclick="verCarrito()">
                    <i class="fas fa-shopping-cart me-1"></i>
                    Carrito <span id="cart-count" class="badge bg-secondary">0</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alerta para mensajes -->
<div id="alerta-productos"></div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="precio-min" class="form-label">Precio Mínimo</label>
                        <input type="range" class="form-range" id="precio-min" min="0" max="1000" value="0" onchange="filtrarProductos()">
                        <span id="precio-min-value">$0</span>
                    </div>
                    <div class="col-md-3">
                        <label for="precio-max" class="form-label">Precio Máximo</label>
                        <input type="range" class="form-range" id="precio-max" min="0" max="1000" value="1000" onchange="filtrarProductos()">
                        <span id="precio-max-value">$1000</span>
                    </div>
                    <div class="col-md-3">
                        <label for="sort-by" class="form-label">Ordenar por</label>
                        <select class="form-select" id="sort-by" onchange="filtrarProductos()">
                            <option value="nombre">Nombre</option>
                            <option value="precio_asc">Precio: Menor a Mayor</option>
                            <option value="precio_desc">Precio: Mayor a Menor</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Stock</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="stock-disponible" checked onchange="filtrarProductos()">
                            <label class="form-check-label" for="stock-disponible">Solo con stock</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Productos -->
<div class="row" id="productos-container">
    <div class="col-12 text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando productos...</span>
        </div>
        <p class="mt-2">Cargando productos...</p>
    </div>
</div>

<!-- Modal del Carrito -->
<div class="modal fade" id="carritoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mi Carrito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="carrito-body">
                <!-- Contenido del carrito se carga via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Seguir comprando</button>
                <button type="button" class="btn btn-primary" onclick="procesarCompra()">Finalizar Compra</button>
            </div>
        </div>
    </div>
</div>

<style>
.producto-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
}
.producto-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.producto-imagen {
    height: 200px;
    object-fit: cover;
    width: 100%;
}
.stock-badge {
    position: absolute;
    top: 10px;
    right: 10px;
}
.cantidad-input {
    width: 60px;
    text-align: center;
}
</style>

<script>
let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
let todosProductos = [];

$(document).ready(function() {
    cargarProductos();
    actualizarContadorCarrito();
});

// === PRODUCTOS ===
function cargarProductos() {
    $('#productos-container').html(`
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando productos...</span>
            </div>
            <p class="mt-2">Cargando productos...</p>
        </div>
    `);

    $.ajax({
        url: '<?php echo site_url('cliente/obtener_productos'); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                todosProductos = response.data;
                mostrarProductos(todosProductos);
            } else {
                $('#productos-container').html(`
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <p>Error al cargar los productos</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#productos-container').html(`
                <div class="col-12 text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <p>Error de conexión</p>
                </div>
            `);
        }
    });
}

function mostrarProductos(productos) {
    if (productos.length === 0) {
        $('#productos-container').html(`
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p>No se encontraron productos</p>
            </div>
        `);
        return;
    }

    let html = '';
    productos.forEach(producto => {
        const enCarrito = carrito.find(item => item.id_producto === producto.id_producto);
        const cantidadCarrito = enCarrito ? enCarrito.cantidad : 0;
        
        html += `
            <div class="col-md-4 mb-4">
                <div class="card producto-card">
                    <div class="position-relative">
                        <img src="${producto.imagen}" class="producto-imagen card-img-top" alt="${producto.nombre}" 
                             onerror="this.src='https://via.placeholder.com/300x200?text=Imagen+no+disponible'">
                        <span class="badge ${producto.stock > 0 ? 'bg-success' : 'bg-danger'} stock-badge">
                            ${producto.stock > 0 ? 'En stock' : 'Sin stock'}
                        </span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">${producto.nombre}</h5>
                        <p class="card-text text-muted">${producto.descripcion}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary">$${producto.precio}</span>
                            <span class="text-muted">Stock: ${producto.stock}</span>
                        </div>
                        <div class="mt-3">
                            ${producto.stock > 0 ? `
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${producto.id_producto}, -1)" ${cantidadCarrito <= 0 ? 'disabled' : ''}>
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="form-control cantidad-input" id="cantidad-${producto.id_producto}" 
                                           value="${cantidadCarrito}" min="0" max="${producto.stock}" 
                                           onchange="actualizarCantidad(${producto.id_producto}, this.value)">
                                    <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${producto.id_producto}, 1)" ${cantidadCarrito >= producto.stock ? 'disabled' : ''}>
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button class="btn btn-primary" onclick="agregarAlCarrito(${producto.id_producto})">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            ` : `
                                <button class="btn btn-outline-secondary w-100" disabled>
                                    Sin stock
                                </button>
                            `}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    $('#productos-container').html(html);
}

// === CARRITO ===
function agregarAlCarrito(idProducto) {
    const producto = todosProductos.find(p => p.id_producto === idProducto);
    const cantidad = parseInt($(`#cantidad-${idProducto}`).val());
    
    if (cantidad <= 0) {
        mostrarAlerta('error', 'Selecciona una cantidad válida');
        return;
    }
    if (cantidad > producto.stock) {
        mostrarAlerta('error', 'No hay suficiente stock disponible');
        return;
    }

    const itemExistente = carrito.find(item => item.id_producto === idProducto);
    if (itemExistente) {
        itemExistente.cantidad = cantidad;
    } else {
        carrito.push({
            id_producto: producto.id_producto,
            nombre: producto.nombre,
            precio: producto.precio,
            imagen: producto.imagen,
            cantidad: cantidad,
            stock: producto.stock
        });
    }

    guardarCarrito();
    actualizarContadorCarrito();
    mostrarAlerta('success', 'Producto agregado al carrito');
}

function cambiarCantidad(idProducto, cambio) {
    const input = $(`#cantidad-${idProducto}`);
    let nuevaCantidad = parseInt(input.val()) + cambio;
    const producto = todosProductos.find(p => p.id_producto === idProducto);
    
    if (nuevaCantidad >= 0 && nuevaCantidad <= producto.stock) {
        input.val(nuevaCantidad);
    }

    // Si ya está en carrito, actualizarlo
    const item = carrito.find(item => item.id_producto === idProducto);
    if (item) {
        item.cantidad = nuevaCantidad;
        guardarCarrito();
        actualizarContadorCarrito();
    }
}

function actualizarCantidad(idProducto, cantidad) {
    cantidad = parseInt(cantidad);
    const producto = todosProductos.find(p => p.id_producto === idProducto);
    
    if (isNaN(cantidad) || cantidad < 0) {
        $(`#cantidad-${idProducto}`).val(0);
    } else if (cantidad > producto.stock) {
        $(`#cantidad-${idProducto}`).val(producto.stock);
        mostrarAlerta('warning', 'No puedes agregar más del stock disponible');
    }
}

function guardarCarrito() {
    localStorage.setItem('carrito', JSON.stringify(carrito));
}

function actualizarContadorCarrito() {
    const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
    $('#cart-count').text(totalItems);
}

function verCarrito() {
    if (carrito.length === 0) {
        mostrarAlerta('info', 'Tu carrito está vacío');
        return;
    }

    let html = '';
    let total = 0;

    carrito.forEach(item => {
        const subtotal = item.precio * item.cantidad;
        total += subtotal;
        
        html += `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="${item.imagen}" class="img-fluid rounded" style="height: 60px; object-fit: cover;" 
                                 onerror="this.src='https://via.placeholder.com/60x60?text=Imagen'">
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-0">${item.nombre}</h6>
                            <small class="text-muted">$${item.precio} c/u</small>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <button class="btn btn-outline-secondary" onclick="cambiarCantidadCarrito(${item.id_producto}, -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="form-control text-center" value="${item.cantidad}" 
                                       onchange="actualizarCantidadCarrito(${item.id_producto}, this.value)" min="1" max="${item.stock}">
                                <button class="btn btn-outline-secondary" onclick="cambiarCantidadCarrito(${item.id_producto}, 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <span class="fw-bold">$${subtotal.toFixed(2)}</span>
                        </div>
                        <div class="col-md-1 text-end">
                            <button class="btn btn-sm btn-danger" onclick="eliminarDelCarrito(${item.id_producto})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    html += `
        <div class="d-flex justify-content-between align-items-center mt-3">
            <h5>Total: $${total.toFixed(2)}</h5>
            <button class="btn btn-danger" onclick="vaciarCarrito()">
                <i class="fas fa-trash me-1"></i>Vaciar Carrito
            </button>
        </div>
    `;

    $('#carrito-body').html(html);
    new bootstrap.Modal(document.getElementById('carritoModal')).show();
}

function cambiarCantidadCarrito(idProducto, cambio) {
    const item = carrito.find(item => item.id_producto === idProducto);
    if (item) {
        const nuevaCantidad = item.cantidad + cambio;
        if (nuevaCantidad >= 1 && nuevaCantidad <= item.stock) {
            item.cantidad = nuevaCantidad;
            guardarCarrito();
            actualizarContadorCarrito();
            verCarrito();
        }
    }
}

function actualizarCantidadCarrito(idProducto, cantidad) {
    cantidad = parseInt(cantidad);
    const item = carrito.find(item => item.id_producto === idProducto);
    if (item && !isNaN(cantidad) && cantidad >= 1 && cantidad <= item.stock) {
        item.cantidad = cantidad;
        guardarCarrito();
        actualizarContadorCarrito();
        verCarrito();
    }
}

function eliminarDelCarrito(idProducto) {
    carrito = carrito.filter(item => item.id_producto !== idProducto);
    guardarCarrito();
    actualizarContadorCarrito();
    cargarProductos();
    verCarrito();
}

function vaciarCarrito() {
    carrito = [];
    guardarCarrito();
    actualizarContadorCarrito();
    cargarProductos();
    $('#carritoModal').modal('hide');
}

function procesarCompra() {
    if (carrito.length === 0) {
        mostrarAlerta('error', 'El carrito está vacío');
        return;
    }

    $('#alerta-productos').html(`
        <div class="alert alert-info">
            <i class="fas fa-spinner fa-spin me-2"></i>
            Procesando compra...
        </div>
    `);

    $.ajax({
        url: '<?php echo site_url('cliente/procesar_compra'); ?>',
        type: 'POST',
        data: { productos: JSON.stringify(carrito) },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                mostrarAlerta('success', '¡Compra realizada exitosamente!');
                carrito = [];
                guardarCarrito();
                actualizarContadorCarrito();
                cargarProductos();
                $('#carritoModal').modal('hide'); // Solo cerrar si éxito
            } else {
                mostrarAlerta('error', response.message || 'Error al procesar la compra');
            }
        },
        error: function() {
            mostrarAlerta('error', 'Error de conexión al procesar la compra');
        }
    });
}

// === FILTROS ===
function buscarProductos() {
    const termino = $('#search-input').val().toLowerCase();
    const productosFiltrados = todosProductos.filter(producto =>
        producto.nombre.toLowerCase().includes(termino) ||
        producto.descripcion.toLowerCase().includes(termino)
    );
    mostrarProductos(productosFiltrados);
}

function filtrarProductos() {
    const precioMin = parseInt($('#precio-min').val());
    const precioMax = parseInt($('#precio-max').val());
    const soloConStock = $('#stock-disponible').is(':checked');
    const orden = $('#sort-by').val();

    $('#precio-min-value').text('$' + precioMin);
    $('#precio-max-value').text('$' + precioMax);

    let productosFiltrados = todosProductos.filter(producto =>
        producto.precio >= precioMin && producto.precio <= precioMax
    );

    if (soloConStock) {
        productosFiltrados = productosFiltrados.filter(producto => producto.stock > 0);
    }

    switch (orden) {
        case 'precio_asc': productosFiltrados.sort((a, b) => a.precio - b.precio); break;
        case 'precio_desc': productosFiltrados.sort((a, b) => b.precio - a.precio); break;
        case 'nombre': productosFiltrados.sort((a, b) => a.nombre.localeCompare(b.nombre)); break;
    }

    mostrarProductos(productosFiltrados);
}

// === ALERTAS ===
function mostrarAlerta(tipo, mensaje) {
    let icono;
    switch (tipo) {
        case 'success': icono = 'check-circle'; break;
        case 'error': icono = 'times-circle'; break;
        case 'warning': icono = 'exclamation-triangle'; break;
        default: icono = 'info-circle';
    }
    
    $('#alerta-productos').html(`
        <div class="alert alert-${tipo === 'error' ? 'danger' : tipo} alert-dismissible fade show" role="alert">
            <i class="fas fa-${icono} me-2"></i>
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
}
</script>
