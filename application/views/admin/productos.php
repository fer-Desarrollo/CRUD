<div class="container mt-4">
    <h2>Gestionar Productos</h2>
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
                <td><?php echo $p->id_producto; ?></td>
                <td><?php echo $p->nombre; ?></td>
                <td><?php echo $p->precio; ?></td>
                <td><?php echo $p->stock; ?></td>
                <td>
                    <button class="btn btn-sm <?php echo $p->activo?'btn-success':'btn-danger'; ?> toggle-estado" data-id="<?php echo $p->id_producto; ?>" data-estado="<?php echo $p->activo?0:1; ?>">
                        <?php echo $p->activo?'Activo':'Inactivo'; ?>
                    </button>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary editar" data-id="<?php echo $p->id_producto; ?>">Editar</button>
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
            <div class="mb-3"><label>Nombre</label><input type="


