<!-- Alerta para mensajes -->
<div id="alerta-dashboard"></div>

<div class="row">
    <!-- Tarjeta de Productos -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100 shadow-sm card-hover">
            <div class="card-body text-center">
                <div class="text-primary mb-3">
                    <i class="fas fa-shopping-bag fa-3x"></i>
                </div>
                <h5 class="card-title">Ver Productos</h5>
                <p class="card-text">Explora nuestro catálogo de productos disponibles</p>
                <button class="btn btn-primary btn-sm" onclick="cargarVista('cliente/productos')">
                    <i class="fas fa-eye me-1"></i>Ver Productos
                </button>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Realizar Compra -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100 shadow-sm card-hover">
            <div class="card-body text-center">
                <div class="text-success mb-3">
                    <i class="fas fa-cart-plus fa-3x"></i>
                </div>
                <h5 class="card-title">Realizar Compra</h5>
                <p class="card-text">Compra los productos que necesites</p>
                <button class="btn btn-success btn-sm" onclick="cargarVista('cliente/productos')">
                    <i class="fas fa-shopping-cart me-1"></i>Comprar Ahora
                </button>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Historial de Compras -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100 shadow-sm card-hover">
            <div class="card-body text-center">
                <div class="text-info mb-3">
                    <i class="fas fa-history fa-3x"></i>
                </div>
                <h5 class="card-title">Historial de Compras</h5>
                <p class="card-text">Revisa tus compras anteriores</p>
                <button class="btn btn-info btn-sm text-white" onclick="cargarVista('cliente/compras')">
                    <i class="fas fa-list-alt me-1"></i>Ver Historial
                </button>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Perfil -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100 shadow-sm card-hover">
            <div class="card-body text-center">
                <div class="text-warning mb-3">
                    <i class="fas fa-user-cog fa-3x"></i>
                </div>
                <h5 class="card-title">Gestionar Perfil</h5>
                <p class="card-text">Actualiza tu información personal</p>
                <button class="btn btn-warning btn-sm" onclick="cargarVista('cliente/perfil')">
                    <i class="fas fa-edit me-1"></i>Editar Perfil
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Compras Recientes (Cargada via AJAX) -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>Compras Recientes
                </h5>
                <button class="btn btn-sm btn-outline-primary" onclick="cargarComprasRecientes()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div class="card-body" id="compras-recientes">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando compras recientes...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Productos Destacados (Cargada via AJAX) -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-star me-2"></i>Productos Destacados
                </h5>
            </div>
            <div class="card-body" id="productos-destacados">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando productos destacados...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}
</style>

<script>
// Función para cargar vistas via AJAX
function cargarVista(url) {
    window.location.href = '<?php echo site_url(); ?>' + url;
}

// Cargar compras recientes al iniciar la página
$(document).ready(function() {
    cargarComprasRecientes();
    cargarProductosDestacados();
});

// Función para cargar compras recientes via AJAX
function cargarComprasRecientes() {
    $('#compras-recientes').html(`
        <div class="text-center py-2">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `);

    $.ajax({
        url: '<?php echo site_url('cliente/obtener_compras_recientes'); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                if (response.data.length > 0) {
                    let html = `
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th># Venta</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Items</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    response.data.forEach(compra => {
                        html += `
                            <tr>
                                <td>${compra.id_venta}</td>
                                <td>${compra.fecha_venta}</td>
                                <td>$${compra.total}</td>
                                <td>${compra.total_items}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" onclick="verDetalleCompra(${compra.id_venta})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    $('#compras-recientes').html(html);
                } else {
                    $('#compras-recientes').html(`
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tienes compras recientes</p>
                            <button class="btn btn-primary" onclick="cargarVista('cliente/productos')">
                                Realizar primera compra
                            </button>
                        </div>
                    `);
                }
            } else {
                $('#compras-recientes').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al cargar compras recientes
                    </div>
                `);
            }
        },
        error: function() {
            $('#compras-recientes').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error de conexión
                </div>
            `);
        }
    });
}

// Función para cargar productos destacados
function cargarProductosDestacados() {
    $('#productos-destacados').html(`
        <div class="text-center py-2">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `);

    $.ajax({
        url: '<?php echo site_url('cliente/obtener_productos_destacados'); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                if (response.data.length > 0) {
                    let html = '<div class="row">';
                    
                    response.data.forEach(producto => {
                        html += `
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <img src="${producto.imagen}" class="card-img-top" alt="${producto.nombre}" style="height: 150px; object-fit: cover;">
                                    <div class="card-body">
                                        <h6 class="card-title">${producto.nombre}</h6>
                                        <p class="card-text text-muted small">${producto.descripcion.substring(0, 60)}...</p>
                                        <p class="card-text"><strong>$${producto.precio}</strong></p>
                                        <button class="btn btn-sm btn-primary w-100" onclick="cargarVista('cliente/productos')">
                                            Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    html += '</div>';
                    $('#productos-destacados').html(html);
                } else {
                    $('#productos-destacados').html(`
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay productos destacados</p>
                        </div>
                    `);
                }
            } else {
                $('#productos-destacados').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al cargar productos
                    </div>
                `);
            }
        },
        error: function() {
            $('#productos-destacados').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error de conexión
                </div>
            `);
        }
    });
}

// Función para ver detalle de compra
function verDetalleCompra(id_venta) {
    window.location.href = '<?php echo site_url('cliente/compras/'); ?>' + id_venta;
}
</script>