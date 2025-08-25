<div class="container">
    <h1 class="mt-4">Bienvenido, <?php echo $usuario->nombre; ?></h1>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Mis Compras</h5>
                    <p class="card-text">Revisa tu historial de compras</p>
                    <a href="<?php echo site_url('cliente/mis_compras'); ?>" class="btn btn-light">Ver Compras</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Productos</h5>
                    <p class="card-text">Explora nuestro catálogo de productos</p>
                    <a href="<?php echo site_url('cliente/productos'); ?>" class="btn btn-light">Ver Productos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Mi Perfil</h5>
                    <p class="card-text">Actualiza tu información personal</p>
                    <a href="<?php echo site_url('cliente/perfil'); ?>" class="btn btn-light">Gestionar Perfil</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Últimas Compras</h4>
                </div>
                <div class="card-body" id="ultimas-compras">
                    <div class="text-center py-3">
                        <div class="spinner-border" role="status" aria-hidden="true"></div>
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Productos Destacados</h4>
                </div>
                <div class="card-body" id="productos-destacados">
                    <div class="text-center py-3">
                        <div class="spinner-border" role="status" aria-hidden="true"></div>
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $.ajax({
        url: '<?php echo site_url("cliente/ajax_obtener_datos_dashboard"); ?>',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            // Últimas compras
            if (response.compras_recientes && response.compras_recientes.length > 0) {
                let comprasHTML = '<div class="list-group">';
                response.compras_recientes.forEach(function(compra) {
                    let fecha = compra.fecha_venta ? new Date(compra.fecha_venta).toLocaleDateString() : '-';
                    comprasHTML += `
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${compra.producto_nombre}</h6>
                                <small>${fecha}</small>
                            </div>
                            <p class="mb-1">Cantidad: ${compra.cantidad} - Total: $${parseFloat(compra.subtotal).toFixed(2)}</p>
                        </div>
                    `;
                });
                comprasHTML += '</div>';
                $('#ultimas-compras').html(comprasHTML);
            } else {
                $('#ultimas-compras').html('<p>No tienes compras recientes.</p>');
            }

            // Productos destacados
            if (response.productos_destacados && response.productos_destacados.length > 0) {
                let productosHTML = '<div class="list-group">';
                response.productos_destacados.forEach(function(producto) {
                    productosHTML += `
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${producto.nombre}</h6>
                                <strong>$${parseFloat(producto.precio).toFixed(2)}</strong>
                            </div>
                            <p class="mb-1">${producto.descripcion}</p>
                            <small>Stock: ${producto.stock}</small>
                        </div>
                    `;
                });
                productosHTML += '</div>';
                $('#productos-destacados').html(productosHTML);
            } else {
                $('#productos-destacados').html('<p>No hay productos destacados.</p>');
            }
        },
        error: function() {
            $('#ultimas-compras').html('<p>Error al cargar las compras.</p>');
            $('#productos-destacados').html('<p>Error al cargar productos.</p>');
        }
    });
});
</script>
