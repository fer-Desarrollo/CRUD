<div class="container mt-4">
    <h2><?= $titulo ?></h2>
    <button class="btn btn-success mb-3" id="btn-nuevo">Nuevo Producto</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Estado</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($productos as $p): ?>
            <tr>
                <td><?= $p->id_producto ?></td>
                <td><?= $p->nombre ?></td>
                <td>$<?= $p->precio ?></td>
                <td><?= $p->stock ?></td>
                <td>
                    <button class="btn btn-sm <?= $p->activo ? 'btn-success' : 'btn-danger' ?> toggle-estado" 
                            data-id="<?= $p->id_producto ?>" 
                            data-estado="<?= $p->activo ? 0 : 1 ?>">
                        <?= $p->activo ? 'Activo' : 'Inactivo' ?>
                    </button>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary editar" data-id="<?= $p->id_producto ?>">Editar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formProducto">
        <div class="modal-header">
          <h5 class="modal-title">Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id_producto" id="id_producto">
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" class="form-control" name="nombre" id="nombre" required>
            </div>
            <div class="mb-3">
                <label>Precio</label>
                <input type="number" step="0.01" class="form-control" name="precio" id="precio" required>
            </div>
            <div class="mb-3">
                <label>Stock</label>
                <input type="number" class="form-control" name="stock" id="stock" required>
            </div>
            <div class="mb-3">
                <label>Imagen URL</label>
                <input type="text" class="form-control" name="imagen" id="imagen">
            </div>
            <div class="mb-3">
                <label>Descripción</label>
                <textarea class="form-control" name="descripcion" id="descripcion"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Nuevo producto
    $('#btn-nuevo').click(function() {
        $('#formProducto')[0].reset();
        $('#id_producto').val('');
        $('#modalProducto .modal-title').text('Nuevo Producto');
        $('#modalProducto').modal('show');
    });

    // Editar producto
    $('.editar').click(function() {
        var id = $(this).data('id');
        $.post('<?= base_url("admin/productos") ?>', {id_producto: id}, function(data) {
            var p = JSON.parse(data);
            $('#id_producto').val(p.id_producto);
            $('#nombre').val(p.nombre);
            $('#precio').val(p.precio);
            $('#stock').val(p.stock);
            $('#imagen').val(p.imagen);
            $('#descripcion').val(p.descripcion);
            $('#modalProducto .modal-title').text('Editar Producto');
            $('#modalProducto').modal('show');
        });
    });

    // Guardar producto
$('#formProducto').submit(function(e) {
    e.preventDefault();

    var nombre = $('#nombre').val().trim();
    var precio = parseFloat($('#precio').val());
    var stock = parseInt($('#stock').val());

    if(nombre === '') {
        alert('El nombre del producto es obligatorio.');
        return;
    }

    if(isNaN(precio) || precio <= 0) {
        alert('El precio debe ser un número mayor a 0.');
        return;
    }

    if(isNaN(stock) || stock < 0) {
        alert('El stock debe ser un número igual o mayor a 0.');
        return;
    }

    // Si todo está bien, enviamos por AJAX
    $.post('<?= base_url("admin/guardar_producto") ?>', $(this).serialize(), function(resp) {
        var res = JSON.parse(resp);
        if(res.success) location.reload();
        else alert(res.message);
    });
});

});
</script>
