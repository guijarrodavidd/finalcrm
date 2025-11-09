<?php
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Obtener nombre y foto del usuario
$usuario_nombre = "Usuario";
$usuario_foto = null;
try {
    $sql = "SELECT nombre, foto FROM usuarios WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado && $row = $resultado->fetch_assoc()) {
            if (!empty($row['nombre'])) {
                $usuario_nombre = $row['nombre'];
            }
            if (!empty($row['foto'])) {
                $usuario_foto = $row['foto'];
            }
        }
    }
} catch (Exception $e) {
    // Silenciar errores
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="principal.php">
            <img src="images/phonehouse.png" alt="PhoneCRM Logo" style="max-width: 100px; height: auto;">
        </a>

        <!-- Botón hamburguesa para móvil -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menú -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav w-100">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'principal') ? 'active' : ''; ?>" href="principal.php">
                        📊 Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'clientes') ? 'active' : ''; ?>" href="clientes.php">
                        👥 Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'actividades') ? 'active' : ''; ?>" href="actividades.php">
                        ✅ Actividades
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'allamar') ? 'active' : ''; ?>" href="allamar.php">
                        📞 ¡A Llamar!
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">
                        🚪 Cerrar Sesión
                    </a>
                </li>
                <!-- Usuario y foto extremo derecho -->
                <li class="nav-item ms-auto d-flex align-items-center ps-3 pe-4">
                    <span class="text-dark font-weight-bold me-4" style="white-space: nowrap;">👤 Usuario: <?php echo htmlspecialchars($usuario_nombre); ?></span>
                    <?php if ($usuario_foto && file_exists($usuario_foto)): ?>
                        <img src="<?php echo htmlspecialchars($usuario_foto); ?>" alt="Perfil" class="rounded-circle" style="width: 45px; height: 45px; border: 2px solid #007bff; object-fit: cover; background: #fff; padding: 2px;">
                    <?php else: ?>
                        <i class="fas fa-user-circle" style="font-size: 45px; color: #007bff; background: #fff; border-radius: 50%; padding: 2px;"></i>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
