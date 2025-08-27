<h5>Venta #: <?= $venta->id_venta ?></h5>
<p>Cliente: <?= $venta->cliente_nombre ?? $venta->id_cliente ?></p>
<p>Fecha: <?= $venta->fecha_venta ?></p>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= $p->nombre ?></td>
                <td><?= $p->cantidad ?></td>
                <td>$<?= number_format($p->precio_unitario, 2) ?></td>
                <td>$<?= number_format($p->subtotal, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No hay productos en esta venta.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<p>Total: $<?= number_format($venta->total, 2) ?></p>
