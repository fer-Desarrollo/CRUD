$(document).ready(function() {
    // Procesar login
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?php echo site_url("auth/procesar_login"); ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = response.redirect;
                } else {
                    $('#message').html('<div class="error">' + response.message + '</div>');
                }
            }
        });
    });

    // Procesar registro
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?php echo site_url("auth/procesar_registro"); ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#message').html('<div class="success">' + response.message + '</div>');
                    setTimeout(function() {
                        window.location.href = '<?php echo site_url("auth/login"); ?>';
                    }, 2000);
                } else {
                    $('#message').html('<div class="error">' + response.message + '</div>');
                }
            }
        });
    });

    // Seleccionar rol
    $('.select-role').on('click', function() {
        var roleCard = $(this).closest('.role-card');
        var id_rol = roleCard.data('rol');
        
        $.ajax({
            url: '<?php echo site_url("auth/establecer_rol"); ?>',
            type: 'POST',
            data: {id_rol: id_rol},
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = response.redirect;
                } else {
                    $('#message').html('<div class="error">' + response.message + '</div>');
                }
            }
        });
    });
});