<div class="container">
    <h1 class="mt-4">Catálogo de Productos</h1>
    
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($this->session->flashdata('exito')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('exito'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>
    
    <div class="row mt-4">
        <div class="col-md-12 mb-3">
            <div class="input-group">
                <input type="text" class="form-control" id="buscar-producto" placeholder="Buscar productos por nombre o descripción...">
                <button class="btn btn-outline-primary" type="button" id="btn-buscar">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
    </div>
    
    <div class="row mt-4" id="lista-productos">
        <?php foreach ($productos as $producto): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 product-card">
                    <div class="image-container" style="height: 250px; overflow: hidden;">
                        <img src="<?php echo $producto->imagen; ?>" class="card-img-top img-fluid" alt="<?php echo $producto->nombre; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo $producto->nombre; ?></h5>
                        <p class="card-text flex-grow-1"><?php echo $producto->descripcion; ?></p>
                        <div class="mt-auto">
                            <p class="card-text"><strong>Precio: $<?php echo number_format($producto->precio, 2); ?></strong></p>
                            <p class="card-text">Disponibles: <?php echo $producto->stock; ?></p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if ($producto->stock > 0): ?>
                            <button class="btn btn-primary w-100 btn-comprar"
                                data-producto-id="<?php echo $producto->id_producto; ?>"
                                data-producto-nombre="<?php echo $producto->nombre; ?>"
                                data-producto-precio="<?php echo $producto->precio; ?>"
                                data-producto-stock="<?php echo $producto->stock; ?>">
                                <i class="fas fa-shopping-cart"></i> Comprar
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>
                                <i class="fas fa-times-circle"></i> Agotado
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalCompra" tabindex="-1" aria-labelledby="modalCompraLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCompraLabel">Confirmar Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres comprar <strong id="producto-nombre-modal"></strong>?</p>
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad:</label>
                    <input type="number" class="form-control" id="cantidad" value="1" min="1" max="1">
                    <small id="stock-disponible" class="form-text text-muted"></small>
                </div>
                <div class="mb-3">
                    <label for="total" class="form-label">Total:</label>
                    <input type="text" class="form-control" id="total" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirmar-compra">Confirmar Compra</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(function() {
    let productoActual = null;
    let precioActual = 0;

    // Buscar productos
    $('#btn-buscar').click(buscarProductos);
    $('#buscar-producto').keypress(function(e) {
        if (e.which == 13) buscarProductos();
    });

    function buscarProductos() {
        const termino = $('#buscar-producto').val();
        $('#lista-productos').html(`
            <div class="col-md-12 text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Buscando productos...</p>
            </div>
        `);

        $.post("<?php echo site_url('cliente/buscar_productos'); ?>", { termino: termino }, function(response) {
            let html = '';
            if (response.length > 0) {
                response.forEach(producto => {
                    html += `
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 product-card">
                                <div class="image-container" style="height:250px;overflow:hidden;">
                                    <img src="${producto.imagen}" class="card-img-top img-fluid" alt="${producto.nombre}" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">${producto.nombre}</h5>
                                    <p class="card-text flex-grow-1">${producto.descripcion}</p>
                                    <div class="mt-auto">
                                        <p class="card-text"><strong>Precio: $${parseFloat(producto.precio).toFixed(2)}</strong></p>
                                        <p class="card-text">Disponibles: ${producto.stock}</p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    ${producto.stock > 0 ?
                                        `<button class="btn btn-primary w-100 btn-comprar"
                                            data-producto-id="${producto.id_producto}"
                                            data-producto-nombre="${producto.nombre}"
                                            data-producto-precio="${producto.precio}"
                                            data-producto-stock="${producto.stock}">
                                            <i class="fas fa-shopping-cart"></i> Comprar
                                        </button>` :
                                        `<button class="btn btn-secondary w-100" disabled>
                                            <i class="fas fa-times-circle"></i> Agotado
                                        </button>`}
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                html = `
                    <div class="col-md-12 text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No se encontraron productos</h4>
                        <p class="text-muted">Intenta con otros términos</p>
                    </div>
                `;
            }
            $('#lista-productos').html(html);
            $('.btn-comprar').click(abrirModalCompra);
        }, "json").fail(function() {
            $('#lista-productos').html(`
                <div class="col-md-12 text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h4 class="text-danger">Error al buscar productos</h4>
                    <p class="text-muted">Intenta nuevamente</p>
                </div>
            `);
        });
    }

    // Abrir modal compra
    function abrirModalCompra() {
        productoActual = $(this).data('producto-id');
        const productoNombre = $(this).data('producto-nombre');
        precioActual = parseFloat($(this).data('producto-precio'));
        const stock = parseInt($(this).data('producto-stock'));

        $('#producto-nombre-modal').text(productoNombre);
        $('#cantidad').val(1).attr('max', stock);
        $('#stock-disponible').text(`Stock disponible: ${stock}`);
        $('#total').val('$' + precioActual.toFixed(2));
        $('#modalCompra').modal('show');
    }

    $('#cantidad').on('input', function() {
        let cantidad = parseInt($(this).val()) || 1;
        const max = parseInt($(this).attr('max'));
        if (cantidad > max) cantidad = max;
        if (cantidad < 1) cantidad = 1;
        $(this).val(cantidad);
        $('#total').val('$' + (precioActual * cantidad).toFixed(2));
    });

    $('#btn-confirmar-compra').click(function() {
        const cantidad = parseInt($('#cantidad').val());
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Procesando...');

        $.post("<?php echo site_url('cliente/comprar_producto'); ?>", {
            id_producto: productoActual,
            cantidad: cantidad
        }, function(response) {
            $('#modalCompra').modal('hide');
            $('#btn-confirmar-compra').prop('disabled', false).html('Confirmar Compra');

            if (response.success) {
                mostrarMensaje('success', response.message);
                setTimeout(buscarProductos, 2000);
            } else {
                mostrarMensaje('error', response.message);
            }
        }, "json").fail(function() {
            $('#modalCompra').modal('hide');
            $('#btn-confirmar-compra').prop('disabled', false).html('Confirmar Compra');
            mostrarMensaje('error', 'Error al procesar la compra');
        });
    });

    function mostrarMensaje(tipo, mensaje) {
        const clase = tipo === 'success' ? 'alert-success' : 'alert-danger';
        const html = `
            <div class="alert ${clase} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('.alert').remove();
        $('.container h1').after(html);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }

    $('.btn-comprar').click(abrirModalCompra);
});
</script>

<style>
.product-card {
    transition: transform .2s, box-shadow .2s;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.image-container {
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-comprar { transition: transform .2s; }
.btn-comprar:hover { transform: scale(1.05); }
.card-body { min-height: 200px; }
</style>
