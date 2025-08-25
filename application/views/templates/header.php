<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? $titulo : 'Sistema de Ventas'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding-top: 80px;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .user-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        .user-info:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .role-badge {
            font-size: 0.7rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <!-- Logo - Redirige al dashboard general -->
            <a class="navbar-brand" href="<?php echo site_url('dashboard'); ?>">
                <i class="fas fa-store me-2"></i>Sistema de Ventas
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto">
                    <?php if ($this->session->userdata('logged_in')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('dashboard'); ?>">
                                <i class="fas fa-home me-1"></i>Inicio
                            </a>
                        </li>

                        <?php 
                        $rol_actual = $this->session->userdata('rol_actual');
                        
                        if ($rol_actual == 3): // Administrador (ID 3) ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo site_url('admin/usuarios'); ?>">
                                    <i class="fas fa-users me-1"></i>Usuarios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo site_url('admin/productos'); ?>">
                                    <i class="fas fa-box me-1"></i>Productos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo site_url('admin/reportes'); ?>">
                                    <i class="fas fa-chart-bar me-1"></i>Reportes
                                </a>
                            </li>

                        <?php elseif ($rol_actual == 1): // Cliente (ID 1) ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo site_url('cliente/productos'); ?>">
                                    <i class="fas fa-shopping-bag me-1"></i>Productos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo site_url('cliente/mis_compras'); ?>">
                                    <i class="fas fa-history me-1"></i>Mis Compras
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo site_url('cliente/perfil'); ?>">
                                    <i class="fas fa-user me-1"></i>Mi Perfil
                                </a>
                            </li>

                        <?php elseif ($rol_actual == 2): // Empleado (ID 2) ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo site_url('empleado/ventas'); ?>">
                                    <i class="fas fa-cash-register me-1"></i>Ventas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo site_url('empleado/inventario'); ?>">
                                    <i class="fas fa-boxes me-1"></i>Inventario
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav">
                    <?php if ($this->session->userdata('logged_in')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-info" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo $this->session->userdata('correo'); ?>
                                <?php if ($this->session->userdata('nombre_rol')): ?>
                                    <span class="role-badge"><?php echo $this->session->userdata('nombre_rol'); ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?php echo site_url('perfil'); ?>">
                                        <i class="fas fa-user me-2"></i>Mi Perfil
                                    </a>
                                </li>
                                <?php 
                                $todos_roles = $this->session->userdata('todos_roles');
                                if (!empty($todos_roles) && is_array($todos_roles) && count($todos_roles) > 1): 
                                ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo site_url('auth/seleccionar_rol'); ?>">
                                            <i class="fas fa-sync me-2"></i>Cambiar Rol
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo site_url('auth/logout'); ?>">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('auth/login'); ?>">
                                <i class="fas fa-sign-in-alt me-1"></i>Ingresar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('auth/register'); ?>">
                                <i class="fas fa-user-plus me-1"></i>Registrarse
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">