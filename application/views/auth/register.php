<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .register-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .form-group {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <h2><i class="fas fa-user-plus me-2"></i>Crear Cuenta</h2>
            <p class="mb-0">Regístrate en nuestro sistema de ventas</p>
        </div>
        
        <div class="register-body">
            <!-- Alerta para mensajes -->
            <div id="alerta"></div>

            <form id="formRegister" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                            <div class="invalid-feedback" id="nombre-error"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                            <div class="invalid-feedback" id="correo-error"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno *</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                            <div class="invalid-feedback" id="apellido_paterno-error"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="apellido_materno" class="form-label">Apellido Materno *</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required>
                            <div class="invalid-feedback" id="apellido_materno-error"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edad" class="form-label">Edad *</label>
                            <input type="number" class="form-control" id="edad" name="edad" min="1" max="120" required>
                            <div class="invalid-feedback" id="edad-error"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono">
                            <div class="invalid-feedback" id="telefono-error"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección</label>
                    <textarea class="form-control" id="direccion" name="direccion" rows="2"></textarea>
                    <div class="invalid-feedback" id="direccion-error"></div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="contrasena" class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                            <span class="password-toggle" onclick="togglePassword('contrasena')">
                                <i class="fas fa-eye"></i>
                            </span>
                            <div class="invalid-feedback" id="contrasena-error"></div>
                            <small class="form-text text-muted">Mínimo 6 caracteres</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required>
                            <span class="password-toggle" onclick="togglePassword('confirmar_contrasena')">
                                <i class="fas fa-eye"></i>
                            </span>
                            <div class="invalid-feedback" id="confirmar_contrasena-error"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="terminos" name="terminos" required>
                    <label class="form-check-label" for="terminos">
                        Acepto los <a href="#" class="text-decoration-none">términos y condiciones</a> *
                    </label>
                    <div class="invalid-feedback" id="terminos-error"></div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-3" id="btnRegister">
                    <span id="btnText">Crear Cuenta</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>

                <div class="text-center">
                    <p class="mb-0">¿Ya tienes una cuenta? 
                        <a href="<?php echo site_url('auth/login'); ?>" class="text-decoration-none">Inicia sesión aquí</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    $(document).ready(function() {
        // Limpiar mensajes de error al escribir
        $('input, textarea').on('input', function() {
            $(this).removeClass('is-invalid');
            $('#' + this.id + '-error').text('');
        });

        $('#formRegister').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const alerta = $('#alerta');
            const btnRegister = $('#btnRegister');
            const btnText = $('#btnText');
            const btnSpinner = $('#btnSpinner');

            // Limpiar errores anteriores
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            alerta.html('');

            // Mostrar loading
            btnRegister.prop('disabled', true);
            btnText.text('Registrando...');
            btnSpinner.removeClass('d-none');

            $.ajax({
                url: '<?php echo site_url('auth/register'); ?>',
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alerta.html(`
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>¡Éxito!</strong> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                        
                        // Redirigir después de 2 segundos
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 2000);
                    } else if (response.status === 'error') {
                        if (response.errors) {
                            // Mostrar errores de validación en cada campo
                            $.each(response.errors, function(campo, mensaje) {
                                $('#' + campo).addClass('is-invalid');
                                $('#' + campo + '-error').text(mensaje);
                            });
                            
                            alerta.html(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong>Error:</strong> Por favor, corrige los errores del formulario.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `);
                        } else {
                            alerta.html(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong>Error:</strong> ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    alerta.html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Error:</strong> Ocurrió un problema al procesar el registro. Intenta nuevamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                },
                complete: function() {
                    // Restaurar botón
                    btnRegister.prop('disabled', false);
                    btnText.text('Crear Cuenta');
                    btnSpinner.addClass('d-none');
                }
            });
        });
    });
    </script>
</body>
</html>