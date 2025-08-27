<div class="container mt-4">
    <h2><?php echo $titulo; ?></h2>
    <button class="btn btn-success mb-3" id="btn-nuevo">Nuevo Usuario</button>

    <table class="table table-bordered" id="tabla-usuarios">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($usuarios as $u): ?>
            <tr>
                <td><?php echo $u->id_usuario; ?></td>
                <td><?php echo $u->nombre.' '.$u->apellido_paterno.' '.$u->apellido_materno; ?></td>
                <td><?php echo $u->correo; ?></td>
                <td><?php echo $u->id_rol; ?></td>
                <td><?php echo $u->activo ? 'Activo' : 'Inactivo'; ?></td>
                <td>
                    <button class="btn btn-primary btn-sm btn-editar" data-id="<?php echo $u->id_usuario; ?>">Editar</button>
                    <?php if($u->activo): ?>
                        <button class="btn btn-danger btn-sm btn-cambiar-estado" data-id="<?php echo $u->id_usuario; ?>" data-activo="0">Desactivar</button>
                    <?php else: ?>
                        <button class="btn btn-success btn-sm btn-cambiar-estado" data-id="<?php echo $u->id_usuario; ?>" data-activo="1">Activar</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal para agregar/editar usuario -->
<div class="modal fade" id="modal-usuario" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="form-usuario">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id_usuario" id="id_usuario">
            <div class="mb-2">
                <label>Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Apellido Paterno</label>
                <input type="text" name="apellido_paterno" id="apellido_paterno" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Apellido Materno</label>
                <input type="text" name="apellido_materno" id="apellido_materno" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Edad</label>
                <input type="number" name="edad" id="edad" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control">
            </div>
            <div class="mb-2">
                <label>Dirección</label>
                <input type="text" name="direccion" id="direccion" class="form-control">
            </div>
            <div class="mb-2">
                <label>Correo</label>
                <input type="email" name="correo" id="correo" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Rol</label>
                <select name="rol" id="rol" class="form-control">
                    <?php foreach($roles as $r): ?>
                        <option value="<?php echo $r->id_rol; ?>"><?php echo $r->nombre_rol; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    // Nuevo usuario
    $('#btn-nuevo').click(function(){
        $('#form-usuario')[0].reset();
        $('#id_usuario').val('');
        $('#modal-usuario').modal('show');
    });

    // Editar usuario
    $('.btn-editar').click(function(){
        let id = $(this).data('id');
        $.post('<?php echo site_url("admin/usuarios"); ?>', {id_usuario:id}, function(data){
            let u = JSON.parse(data);
            $('#id_usuario').val(u.id_usuario);
            $('#nombre').val(u.nombre);
            $('#apellido_paterno').val(u.apellido_paterno);
            $('#apellido_materno').val(u.apellido_materno);
            $('#edad').val(u.edad);
            $('#telefono').val(u.telefono);
            $('#direccion').val(u.direccion);
            $('#correo').val(u.correo);
            $('#rol').val(u.id_rol);
            $('#modal-usuario').modal('show');
        });
    });

    // Cambiar estado
    $('.btn-cambiar-estado').click(function(){
        let id = $(this).data('id');
        let activo = $(this).data('activo');
        $.get('<?php echo site_url("admin/cambiar_estado_usuario"); ?>/'+id+'/'+activo, function(){
            location.reload();
        });
    });

    // Guardar usuario
    $('#form-usuario').submit(function(e){
        e.preventDefault();
        $.post('<?php echo site_url("admin/guardar_usuario"); ?>', $(this).serialize(), function(data){
            let res = JSON.parse(data);
            if(res.success){
                alert(res.message);
                location.reload();
            }
        });
    });

});
</script>
