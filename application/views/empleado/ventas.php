<?php $this->load->view('templates/header'); ?>

<div class="container mt-5">
    <h1 class="mb-4"><?= isset($titulo) ? $titulo : 'Historial de Ventas' ?></h1>

    <div id="alert-msg"></div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID Venta</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($ventas)): ?>
                <?php foreach ($ventas as $venta): ?>
                <tr id="venta-<?= $venta->id_venta ?>">
                    <td><?= $venta->id_venta ?></td>
                    <td><?= isset($venta->cliente_nombre) ? $venta->cliente_nombre : $venta->id_cliente ?></td>
                    <td><?= $venta->fecha_venta ?></td>
                    <td>$<?= number_format($venta->total, 2) ?></td>
                    <td>
                        <button class="btn btn-info btn-sm view-detail" data-id="<?= $venta->id_venta ?>">Ver detalle</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No hay ventas registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal para detalle de venta -->
    <div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="detalleModalLabel">Detalle de Venta</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body" id="detalleContent">
            <!-- Aquí se cargará el detalle vía AJAX -->
          </div>
        </div>
      </div>
    </div>
</div>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(document).ready(function() {
    $(document).on('click', '.view-detail', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '<?= site_url("empleado/detalle_venta/") ?>' + id,
            method: 'GET',
            success: function(response) {
                $('#detalleContent').html(response);
                var detalleModal = new bootstrap.Modal(document.getElementById('detalleModal'));
                detalleModal.show();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error, xhr.responseText);
                $('#alert-msg').html('<div class="alert alert-danger">Error al cargar el detalle</div>');
            }
        });
    });
});
</script>

<?php $this->load->view('templates/footer'); ?>
