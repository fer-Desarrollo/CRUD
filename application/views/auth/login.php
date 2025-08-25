<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary">Bienvenido</h2>
                            <p class="text-muted">Inicia sesión en tu cuenta</p>
                        </div>
                        
                        <!-- ALERTA -->
                        <div id="alerta"></div>

                        <form id="formLogin" method="POST" action="<?php echo site_url('auth/login'); ?>">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
                                <div class="invalid-feedback" id="correo-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                                <div class="invalid-feedback" id="contrasena-error"></div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Recordarme</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3" id="btnLogin">
                                <span id="btnText">Iniciar Sesión</span>
                                <div id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">¿No tienes una cuenta? 
                                <a class="text-decoration-none" href="<?php echo site_url('auth/register'); ?>">Regístrate</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function(){
        // Limpiar mensajes de error al escribir
        $("input").on("input", function(){
            $(this).removeClass("is-invalid");
            $("#" + this.id + "-error").text("");
        });
        
        $("#formLogin").on("submit", function(e){
            e.preventDefault();
            let form = $(this);
            let alerta = $("#alerta");
            let btnLogin = $("#btnLogin");
            let btnText = $("#btnText");
            let btnSpinner = $("#btnSpinner");
            
            // Limpiar errores anteriores
            $(".is-invalid").removeClass("is-invalid");
            $(".invalid-feedback").text("");
            alerta.html("");

            // Mostrar loading
            btnLogin.prop("disabled", true);
            btnText.text("Iniciando sesión...");
            btnSpinner.removeClass("d-none");

            $.ajax({
                url: form.attr("action"),
                type: "POST",
                data: form.serialize(),
                dataType: "json",
                success: function(response){
                    // Restaurar botón
                    btnLogin.prop("disabled", false);
                    btnText.text("Iniciar Sesión");
                    btnSpinner.addClass("d-none");
                    
                    if(response.status === 'success'){
                        alerta.html('<div class="alert alert-success">'+response.message+'</div>');
                        setTimeout(function(){
                            window.location.href = response.redirect;
                        }, 1500);
                    } else if(response.status === 'error'){
                        if(response.errors){
                            // Mostrar errores de validación en cada campo
                            $.each(response.errors, function(campo, mensaje){
                                $("#" + campo).addClass("is-invalid");
                                $("#" + campo + "-error").text(mensaje);
                            });
                            alerta.html('<div class="alert alert-danger">Por favor, corrige los errores del formulario.</div>');
                        } else {
                            alerta.html('<div class="alert alert-danger">'+response.message+'</div>');
                        }
                    } else if(response.status === 'info') {
                        // Ya está logueado
                        alerta.html('<div class="alert alert-info">'+response.message+'</div>');
                        setTimeout(function(){
                            window.location.href = response.redirect;
                        }, 1500);
                    }
                },
                error: function(xhr, status, error){
                    // Restaurar botón
                    btnLogin.prop("disabled", false);
                    btnText.text("Iniciar Sesión");
                    btnSpinner.addClass("d-none");
                    
                    console.error("Error:", error);
                    alerta.html('<div class="alert alert-danger">Ocurrió un error inesperado. Intenta nuevamente.</div>');
                }
            });
        });
    });
    </script>
</body>
</html>