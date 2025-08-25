<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Rol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-3px);
        }
        .admin-card {
            border-left: 4px solid #dc3545;
        }
        .client-card {
            border-left: 4px solid #0d6efd;
        }
        .employee-card {
            border-left: 4px solid #198754;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-primary">Selecciona un Rol</h3>
                            <p class="text-muted">Elige cómo deseas acceder al sistema</p>
                            
                            <!-- Alerta para mensajes -->
                            <div id="alerta" class="my-3"></div>
                        </div>
                        
                        <div class="row g-3">
                            <?php 
                            // Mostrar solo los roles disponibles para el usuario
                            foreach ($roles as $rol): 
                                // Asignar clase según el tipo de rol
                                $cardClass = '';
                                if ($rol->id_rol == 1) $cardClass = 'admin-card';
                                elseif ($rol->id_rol == 2) $cardClass = 'client-card';
                                elseif ($rol->id_rol == 3) $cardClass = 'employee-card';
                            ?>
                            <div class="col-md-6">
                                <div class="card h-100 <?php echo $cardClass; ?> role-option" data-role-id="<?php echo $rol->id_rol; ?>">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><?php echo $rol->nombre_rol; ?></h5>
                                        <p class="card-text text-muted small">
                                            <?php 
                                            // Descripción según el rol
                                            if ($rol->id_rol == 1) echo 'Acceso completo al sistema';
                                            elseif ($rol->id_rol == 2) echo 'Acceso a funciones de cliente';
                                            elseif ($rol->id_rol == 3) echo 'Acceso a funciones de empleado';
                                            ?>
                                        </p>
                                        <button class="btn btn-outline-primary btn-sm">Seleccionar</button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Formulario oculto para enviar la selección -->
                        <form id="formSeleccionarRol" method="POST" action="<?php echo site_url('auth/seleccionar_rol'); ?>" class="d-none">
                            <input type="hidden" name="rol_id" id="rol_id">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Manejar clic en las opciones de rol
        $('.role-option').on('click', function() {
            const rolId = $(this).data('role-id');
            $('#rol_id').val(rolId);
            
            // Mostrar loading
            $('#alerta').html('<div class="alert alert-info">Procesando...</div>');
            
            // Enviar formulario por AJAX
            $.ajax({
                url: $('#formSeleccionarRol').attr('action'),
                type: 'POST',
                data: $('#formSeleccionarRol').serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#alerta').html('<div class="alert alert-success">' + response.message + '</div>');
                        
                        // Redirigir después de breve espera
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1000);
                    } else {
                        $('#alerta').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#alerta').html('<div class="alert alert-danger">Error al procesar la solicitud</div>');
                }
            });
        });
    });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>