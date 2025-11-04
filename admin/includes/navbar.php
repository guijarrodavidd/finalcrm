<?php
// Obtener rol del usuario actual
$sql_rol = "SELECT rol FROM usuarios WHERE id = ?";
$stmt_rol = $conexion->prepare($sql_rol);
$stmt_rol->bind_param("i", $_SESSION['usuario_id']);
$stmt_rol->execute();
$resultado_rol = $stmt_rol->get_result()->fetch_assoc();
$es_admin = $resultado_rol['rol'] === 'encargado';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="principal.php">
            <img src="..../images/phonehouse.png" alt="PhoneCRM Logo" style="max-width: 100px; height: auto;">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="principal.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                
                <!-- COMPROBACIÓN DE ROL -->
                <?php if ($es_admin): ?>
                    <!-- ENLACE PARA ADMIN -->
                    <li class="nav-item">
                        <a class="nav-link" href="./clientes.php">
                            <i class="fas fa-users"></i> Clientes (Admin)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./actividades.php">
                            <i class="fas fa-tasks"></i> Actividades
                        </a>
                    </li>
                <?php else: ?>
                    <!-- ENLACES PARA TIENDA (PÚBLICOS) -->
                    <li class="nav-item">
                        <a class="nav-link" href="clientes.php">
                            <i class="fas fa-users"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="actividades.php">
                            <i class="fas fa-tasks"></i> Actividades
                        </a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
