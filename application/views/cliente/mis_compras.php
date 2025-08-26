<div class="container mt-5">
    <h1 class="mb-4">🛒 Mis Compras</h1>

    <!-- Contenedor para mostrar alertas dinámicas -->
    <div id="alert-container"></div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-compras">
                        <!-- Aquí se insertan las compras con AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mensaje cuando no hay compras -->
    <div id="sin-compras" class="text-center py-5 d-none">
        <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">Aún no has realizado compras</h4>
        <p class="text-muted">Cuando compres productos, aparecerán aquí.</p>
        <a href="<?= site_url('cliente/catalogo'); ?>" class="btn btn-primary mt-3">
            <i class="fas fa-shopping-cart"></i> Ir al catálogo
        </a>
    </div>
</div>

<!-- JQuery + Script de AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Cargar compras al inicio
    cargarCompras();

    // Función para cargar compras con AJAX
    function cargarCompras() {
        $.ajax({
            url: "<?= site_url('cliente/obtener_mis_compras'); ?>",
            type: "GET",
            dataType: "json",
            success: function (compras) {
                let tbody = $("#tabla-compras");
                tbody.empty();

                if (compras.length > 0) {
                    $("#sin-compras").addClass("d-none");
                    let i = 1;
                    $.each(compras, function (index, compra) {
                        let fila = `
                            <tr id="fila-${compra.id_detalle}">
                                <td>${i++}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="${compra.producto_imagen}" alt="${compra.producto}" 
                                             class="rounded me-2" style="width: 60px; height: 60px; object-fit: cover;">
                                        <span>${compra.producto}</span>
                                    </div>
                                </td>
                                <td>${compra.cantidad}</td>
                                <td>$${parseFloat(compra.precio_unitario).toFixed(2)}</td>
                                <td><strong>$${(compra.precio_unitario * compra.cantidad).toFixed(2)}</strong></td>
                                <td>${formatearFecha(compra.fecha_venta)}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger btn-eliminar" 
                                            data-id="${compra.id_detalle}">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.append(fila);
                    });
                } else {
                    $("#sin-compras").removeClass("d-none");
                }
            },
            error: function () {
                mostrarAlerta("danger", "Error al cargar tus compras.");
            }
        });
    }

    // Evento para eliminar con delegación (porque las filas son dinámicas)
    $(document).on("click", ".btn-eliminar", function () {
        let id = $(this).data("id");
        let fila = $("#fila-" + id);

        if (confirm("¿Seguro que quieres eliminar esta compra?")) {
            $.ajax({
                url: "<?= site_url('cliente/eliminar_compra/'); ?>" + id,
                type: "POST",
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        fila.fadeOut(500, function(){ $(this).remove(); });
                        mostrarAlerta("success", response.message);

                        // Si ya no hay filas -> mostrar mensaje "sin compras"
                        if ($("#tabla-compras tr").length === 0) {
                            $("#sin-compras").removeClass("d-none");
                        }
                    } else {
                        mostrarAlerta("danger", response.message);
                    }
                },
                error: function () {
                    mostrarAlerta("danger", "Error al procesar la solicitud.");
                }
            });
        }
    });

    // Función para mostrar alertas Bootstrap
    function mostrarAlerta(tipo, mensaje) {
        let alerta = `
            <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        `;
        $("#alert-container").html(alerta);
    }

    // Formatear fecha
    function formatearFecha(fechaStr) {
        let fecha = new Date(fechaStr);
        return fecha.toLocaleDateString("es-MX") + " " + fecha.toLocaleTimeString("es-MX", {hour: '2-digit', minute:'2-digit'});
    }
});
</script>
