<div class="container mt-5">
    <h1 class="mb-4">🛒 Mis Compras</h1>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('exito')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('exito'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($compras)): ?>
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
                        <tbody>
                            <?php $i = 1; foreach ($compras as $compra): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= $compra->producto_imagen; ?>" alt="<?= $compra->producto; ?>" 
                                                 class="rounded me-2" style="width: 60px; height: 60px; object-fit: cover;">
                                            <span><?= $compra->producto; ?></span>
                                        </div>
                                    </td>
                                    <td><?= $compra->cantidad; ?></td>
                                    <td>$<?= number_format($compra->precio_unitario, 2); ?></td>
                                    <td><strong>$<?= number_format($compra->precio_unitario * $compra->cantidad, 2); ?></strong></td>
                                    <td><?= date("d/m/Y H:i", strtotime($compra->fecha_venta)); ?></td>
                                    <td>
                                        <form action="<?= site_url('cliente/eliminar_compra/'.$compra->id_detalle); ?>" method="post" onsubmit="return confirm('¿Seguro que quieres eliminar esta compra?');">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Aún no has realizado compras</h4>
            <p class="text-muted">Cuando compres productos, aparecerán aquí.</p>
            <a href="<?= site_url('cliente/catalogo'); ?>" class="btn btn-primary mt-3">
                <i class="fas fa-shopping-cart"></i> Ir al catálogo
            </a>
        </div>
    <?php endif; ?>
</div>
