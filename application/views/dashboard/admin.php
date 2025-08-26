<div class="container mt-4">
    <h1>Panel del Administrador</h1>
    <p>Bienvenido al panel de control para administradores.</p>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Usuarios</h5>
                    <a href="<?php echo site_url('admin/usuarios'); ?>" class="btn btn-light">Gestionar</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Productos</h5>
                    <a href="<?php echo site_url('admin/productos'); ?>" class="btn btn-light">Gestionar</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Reportes</h5>
                    <a href="<?php echo site_url('admin/reportes'); ?>" class="btn btn-light">Ver</a>
                </div>
            </div>
        </div>
    </div>
</div>
