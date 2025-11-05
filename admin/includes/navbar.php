<?php
// Obtener rol del usuario actual
$sql_rol = "SELECT rol FROM usuarios WHERE id = ?";
$stmt_rol = $conexion->prepare($sql_rol);
$stmt_rol->bind_param("i", $_SESSION['usuario_id']);
$stmt_rol->execute();
$resultado_rol = $stmt_rol->get_result()->fetch_assoc();
$es_admin = $resultado_rol['rol'] === 'encargado';

// Detectar la página actual
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="principal.php">
            <img src="../../images/phonehouse.png" alt="PhoneCRM Logo" style="max-width: 100px; height: auto;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'principal') ? 'active' : ''; ?>" aria-current="page" href="principal.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                
                <!-- COMPROBACIÓN DE ROL -->
                <?php if ($es_admin): ?>
                    <!-- ENLACE PARA ADMIN -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'clientes') ? 'active' : ''; ?>" href="clientes.php">
                            <i class="fas fa-users"></i> Clientes (Admin)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'actividades') ? 'active' : ''; ?>" href="actividades.php">
                            <i class="fas fa-tasks"></i> Actividades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../../logout.php">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </li>
                <?php else: ?>
                    <!-- ENLACES PARA TIENDA (PÚBLICOS) -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'clientes') ? 'active' : ''; ?>" href="clientes.php">
                            <i class="fas fa-users"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'actividades') ? 'active' : ''; ?>" href="actividades.php">
                            <i class="fas fa-tasks"></i> Actividades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'allamar') ? 'active' : ''; ?>" href="allamar.php">
                            <i class="fas fa-phone-alt"></i> A Llamar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
