<?php
session_start();
include("./admin/includes/database.php");
require_once "./admin/includes/crudClientes.php";
require_once "./admin/includes/crudActividades.php";

$connClass = new Connection();
$conexion = $connClass->getConnection();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$crud = new crudClientes($conexion, $usuario_id);
$crudAct = new crudActividades($conexion, $usuario_id);

// Marcar completada
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['marcar_completada'])) {
    $actividad_id = intval($_POST['actividad_id']);
    $estado = intval($_POST['estado']);
    $cliente_id = intval($_POST['cliente_id'] ?? 0);
    
    $crudAct->marcarCompletada($actividad_id, $estado);
    if ($cliente_id > 0) {
        $crud->actualizarUltimaVisita($cliente_id);
    }
}

// Obtener índice del cliente actual
$indice_actual = isset($_GET['index']) ? intval($_GET['index']) : 0;

// Obtener todos los clientes del usuario
$todos_clientes = $crud->getClientes();
$total_clientes = $todos_clientes->num_rows;

if ($total_clientes == 0) {
    header("Location: clientes.php");
    exit();
}

// Validar índice
if ($indice_actual < 0) $indice_actual = 0;
if ($indice_actual >= $total_clientes) $indice_actual = $total_clientes - 1;

// Obtener cliente actual
$todos_clientes->data_seek($indice_actual);
$cliente = $todos_clientes->fetch_assoc();

if (!$cliente) {
    header("Location: allamar.php?index=0");
    exit();
}

// Actualizar última visita
$crud->actualizarUltimaVisita($cliente['id']);

// Obtener datos del cliente
$actividades = $crudAct->getActividades($cliente['id']);
$proxima_actividad = $crudAct->getProximaActividad($cliente['id']);
$etiquetas = $crud->getEtiquetas($cliente['id']);

// Calcular índices anterior y siguiente
$indice_anterior = $indice_actual - 1;
$indice_siguiente = $indice_actual + 1;
$hay_anterior = $indice_anterior >= 0;
$hay_siguiente = $indice_siguiente < $total_clientes;

// Variables para las fechas
$hoy = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PhoneCRM - A Llamar</title>
    <link href="startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
</head>
<body>

    <!-- Navbar -->
    <?php include("./admin/includes/navbar.php"); ?>

    <!-- Contenedor principal -->
    <div class="container-fluid" style="margin-top: 20px; margin-bottom: 20px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Contador y botón volver -->
                <div class="row mb-3">
                    <div class="col-6">
                        <a href="clientes.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                    <div class="col-6 text-right">
                        <span class="badge badge-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            <?php echo ($indice_actual + 1) . ' / ' . $total_clientes; ?>
                        </span>
                    </div>
                </div>

                <!-- Tarjeta del cliente -->
                <div class="card shadow">
                    <!-- Header -->
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-user-circle"></i> 
                            <?php echo htmlspecialchars($cliente['nombre_apellidos']); ?>
                        </h3>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <!-- Info Principal -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Teléfono:</strong></p>
                                <p>
                                    <a href="tel:<?php echo htmlspecialchars($cliente['telefono']); ?>">
                                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($cliente['telefono']); ?>
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>DNI:</strong></p>
                                <p><?php echo htmlspecialchars($cliente['dni']); ?></p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Convergente:</strong></p>
                                <p><?php echo !empty($cliente['convergente']) ? htmlspecialchars($cliente['convergente']) : '-'; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Cliente Desde:</strong></p>
                                <p><?php echo date('d/m/Y', strtotime($cliente['fecha_creacion'])); ?></p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Última Visita:</strong></p>
                                <p><?php echo $cliente['ultima_visita'] ? date('d/m/Y H:i', strtotime($cliente['ultima_visita'])) : 'Sin visitas'; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Etiquetas:</strong></p>
                                <p>
                                    <?php 
                                    if ($etiquetas->num_rows > 0):
                                        while ($etiqueta = $etiquetas->fetch_assoc()):
                                    ?>
                                        <span class="badge" style="background-color: <?php echo htmlspecialchars($etiqueta['color']); ?>; color: white; font-size: 0.9rem; padding: 0.4rem 0.8rem; margin-right: 0.3rem;">
                                            <?php echo htmlspecialchars($etiqueta['nombre']); ?>
                                        </span>
                                    <?php
                                        endwhile;
                                    else:
                                    ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <!-- Próxima Actividad -->
                        <?php if ($proxima_actividad): 
                            $color = crudClientes::getColorActividad($proxima_actividad['fecha'], $proxima_actividad['completada']);
                            $fecha_formato = date('d/m/Y', strtotime($proxima_actividad['fecha']));
                        ?>
                            <div class="alert alert-<?php 
                                echo $color === 'hoy' ? 'success' : ($color === 'pasada' ? 'danger' : 'info'); 
                            ?>" role="alert">
                                <h5 class="alert-heading">
                                    <i class="fas fa-bell"></i> Próxima Actividad
                                </h5>
                                <strong><?php echo $proxima_actividad['tipo']; ?></strong> - <?php echo $fecha_formato; ?>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <!-- Historial de Actividades -->
                        <h5><i class="fas fa-list"></i> Historial de Actividades</h5>
                        <?php
                        $actividades->data_seek(0);
                        if ($actividades->num_rows > 0):
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Descripción</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        while ($act = $actividades->fetch_assoc()): 
                                            $color = crudClientes::getColorActividad($act['fecha'], $act['completada']);
                                            $fecha_formato = date('d/m/Y', strtotime($act['fecha']));
                                        ?>
                                        <tr class="<?php echo ($act['completada'] == 1) ? 'actividad-completada-row' : ''; ?>">
                                            <td><strong><?php echo $act['tipo']; ?></strong></td>
                                            <td><?php echo !empty($act['descripcion']) ? htmlspecialchars($act['descripcion']) : '-'; ?></td>
                                            <td>
                                                <span class="badge badge-<?php 
                                                    if ($act['completada']) {
                                                        echo 'secondary';
                                                    } elseif ($color === 'pasada') {
                                                        echo 'danger';
                                                    } elseif ($color === 'hoy') {
                                                        echo 'success';
                                                    } else {
                                                        echo 'info';
                                                    }
                                                ?>">
                                                    <?php echo $fecha_formato; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($act['completada'] == 1): ?>
                                                    <span class="badge badge-success">Completada</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Pendiente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="actividad_id" value="<?php echo $act['id']; ?>">
                                                    <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                                                    <input type="hidden" name="estado" value="<?php echo $act['completada'] == 1 ? 0 : 1; ?>">
                                                    <button type="submit" name="marcar_completada" class="btn btn-sm <?php echo $act['completada'] == 1 ? 'btn-warning' : 'btn-success'; ?>">
                                                        <i class="fas fa-<?php echo $act['completada'] == 1 ? 'undo' : 'check'; ?>">Marcar como completado</i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center">Sin actividades registradas</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Botones de navegación -->
                <div class="row mt-4 mb-4">
                    <div class="col-6 text-left">
                        <?php if ($hay_anterior): ?>
                            <a href="allamar.php?index=<?php echo $indice_anterior; ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-chevron-left"></i> Anterior
                            </a>
                        <?php else: ?>
                            <button class="btn btn-primary btn-lg" disabled>
                                <i class="fas fa-chevron-left"></i> Anterior
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="col-6 text-right">
                        <?php if ($hay_siguiente): ?>
                            <a href="allamar.php?index=<?php echo $indice_siguiente; ?>" class="btn btn-primary btn-lg">
                                Siguiente <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-primary btn-lg" disabled>
                                Siguiente <i class="fas fa-chevron-right"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js"></script>
    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
