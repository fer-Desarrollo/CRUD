
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    body {
        background-color: #f1f3f5;
    }

    .dashboard-container {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        justify-content: center;
        margin-top: 40px;
    }

    .card-custom {
        width: 230px;
        border-radius: 15px;
        text-align: center;
        padding: 25px 15px;
        color: #fff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }

    .card-custom i {
        font-size: 3rem;
        margin-bottom: 15px;
        transition: transform 0.3s ease, color 0.3s ease;
    }

    .card-custom:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.2);
    }

    .card-custom:hover i {
        transform: scale(1.2);
    }

    /* Colores más elegantes y suaves */
    .card-ventas { background: #4CAF50; }       /* Verde moderado */
    .card-inventario { background: #FF9800; }   /* Naranja cálido */
    .card-clientes { background: #00BCD4; }     /* Cyan */
    .card-reportes { background: #3F51B5; }     /* Azul profundo */

    .card-body h5 {
        font-weight: 600;
        font-size: 1.2rem;
    }

    .card-body p {
        font-size: 0.9rem;
    }

    /* Botón elegante */
    .btn-card {
        margin-top: 10px;
        padding: 6px 18px;
        border-radius: 25px;
        font-weight: 500;
        font-size: 0.9rem;
        color: #fff;
        background-color: rgba(255,255,255,0.2);
        border: none;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .btn-card:hover {
        background-color: rgba(255,255,255,0.35);
        transform: scale(1.05);
    }
</style>

<div class="container">
    <h1 class="text-center mt-5"><?= $titulo ?></h1>

    <div class="dashboard-container">

        <!-- Ver Ventas -->
        <div class="card card-custom card-ventas" onclick="location.href='<?= base_url('empleado/ventas') ?>'">
            <i class="bi bi-cash-stack"></i>
            <div class="card-body">
                <h5 class="card-title">Ver Ventas</h5>
                <p class="card-text">Revisar historial de ventas y detalles.</p>
                <button class="btn btn-card btn-sm">Ir</button>
            </div>
        </div>

        <!-- Gestionar Inventario -->
        <div class="card card-custom card-inventario" onclick="location.href='<?= base_url('empleado/inventario') ?>'">
            <i class="bi bi-box-seam"></i>
            <div class="card-body">
                <h5 class="card-title">Gestionar Inventario</h5>
                <p class="card-text">Actualizar stock de productos.</p>
                <button class="btn btn-card btn-sm">Ir</button>
            </div>
        </div>

    </div>
</div>
