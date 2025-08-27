<div class="container mt-5">
    <h1 class="mb-4"><?= $titulo ?></h1>

    <div id="alert-msg"></div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $producto): ?>
            <tr id="producto-<?= $producto->id_producto ?>">
                <td><?= $producto->id_producto ?></td>
                <td><?= $producto->nombre ?></td>
                <td><?= $producto->descripcion ?></td>
                <td>$<?= number_format($producto->precio, 2) ?></td>
                <td>
                    <input type="number" class="form-control form-control-sm stock-input" 
                           data-id="<?= $producto->id_producto ?>" 
                           value="<?= $producto->stock ?>" min="0">
                </td>
                <td>
                    <button class="btn btn-warning btn-sm update-stock" data-id="<?= $producto->id_producto ?>">Actualizar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script>
$(document).ready(function() {
    // Delegación de eventos
    $(document).on('click', '.update-stock', function() {
        var id = $(this).data('id');
        var stock = $('#producto-' + id + ' .stock-input').val();

        // Validación rápida
        if(stock === '' || isNaN(stock) || stock < 0){
            alert('Ingresa un stock válido');
            return;
        }

        $.ajax({
            url: '<?= site_url("empleado/actualizar_stock/") ?>' + id,
            type: 'POST',
            data: { stock: stock },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    $('#alert-msg').html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    $('#alert-msg').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
                setTimeout(function() {
                    $('#alert-msg').html('');
                }, 3000);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error, xhr.responseText);
                $('#alert-msg').html('<div class="alert alert-danger">Error en la solicitud AJAX</div>');
                setTimeout(function() {
                    $('#alert-msg').html('');
                }, 3000);
            }
        });
    });
});
</script>
