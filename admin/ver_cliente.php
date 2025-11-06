<?php
session_start();
include("./includes/database.php");
require_once "./includes/crudClientes.php";
require_once "./includes/crudActividades.php";

$connClass = new Connection();
$conexion = $connClass->getConnection();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Verificar que sea admin
$sql = "SELECT rol FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$resultado = $stmt->get_result()->fetch_assoc();

if (!$resultado || $resultado['rol'] !== 'encargado') {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$crud = new crudClientes($conexion, $usuario_id);
$crudAct = new crudActividades($conexion, $usuario_id);

$id = $_GET['id'] ?? null;
$cliente = null;

if (!$id) {
    header("Location: clientes.php");
    exit();
}

// Obtener cliente (sin restricción de usuario_id porque es admin)
$sql = "SELECT * FROM clientes WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();

if (!$cliente) {
    header("Location: clientes.php");
    exit();
}

// Actualizar última visita
$crud->actualizarUltimaVisita($id);

// Obtener actividades
$actividades = $crudAct->getActividades($cliente['id']);

// Obtener etiquetas
$etiquetas = $crud->getEtiquetasClienteCompletas($cliente['id']);

// Obtener próxima actividad
$proxima_actividad = $crudAct->getProximaActividad($cliente['id']);

// Obtener nombre de la tienda
$sql_tienda = "SELECT nombre FROM usuarios WHERE id = ?";
$stmt_tienda = $conexion->prepare($sql_tienda);
$stmt_tienda->bind_param("i", $cliente['usuario_id']);
$stmt_tienda->execute();
$tienda = $stmt_tienda->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PhoneCRM - Detalles del Cliente</title>
    <link href="../startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/favicon.png">

</head>
<body id="page-top">

    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("./includes/navbar.php"); ?>
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
                        <h1 class="h3 mb-0 text-gray-800">Detalles del Cliente</h1>
                        <a href="clientes.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>

                    <!-- Información Principal -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-primary">
                                    <h6 class="m-0 font-weight-bold text-white">
                                        <i class="fas fa-user-circle"></i> Información Personal
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p><strong>Nombre Completo:</strong></p>
                                            <h5><?php echo htmlspecialchars($cliente['nombre_apellidos']); ?></h5>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Tienda:</strong></p>
                                            <h5><?php echo htmlspecialchars($tienda['nombre'] ?? 'N/A'); ?></h5>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p><strong>ID Cliente:</strong></p>
                                            <p><?php echo $cliente['id']; ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>DNI:</strong></p>
                                            <p><?php echo !empty($cliente['dni']) ? htmlspecialchars($cliente['dni']) : '<span class="text-muted">-</span>'; ?></p>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p><strong>Teléfono:</strong></p>
                                            <p>
                                                <a href="tel:<?php echo htmlspecialchars($cliente['telefono']); ?>">
                                                    <?php echo htmlspecialchars($cliente['telefono']); ?>
                                                </a>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Convergente:</strong></p>
                                            <p><?php echo !empty($cliente['convergente']) ? htmlspecialchars($cliente['convergente']) : '<span class="text-muted">-</span>'; ?></p>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Fecha de Creación:</strong></p>
                                            <p><?php echo date('d/m/Y H:i', strtotime($cliente['fecha_creacion'])); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Última Visita:</strong></p>
                                            <p>
                                                <?php 
                                                if ($cliente['ultima_visita']) {
                                                    echo date('d/m/Y H:i', strtotime($cliente['ultima_visita']));
                                                } else {
                                                    echo '<span class="text-muted">Sin visitas</span>';
                                                }
                                                ?>
                                            </p>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-12">
                                            <p><strong>Etiquetas:</strong></p>
                                            <div>
                                                <?php
                                                if ($etiquetas->num_rows > 0):
                                                    while ($etiqueta = $etiquetas->fetch_assoc()):
                                                ?>
                                                    <span class="badge" style="background-color: <?php echo htmlspecialchars($etiqueta['color']); ?>;">
                                                        <?php echo htmlspecialchars($etiqueta['nombre']); ?>
                                                    </span>
                                                <?php
                                                    endwhile;
                                                else:
                                                ?>
                                                    <span class="text-muted">Sin etiquetas</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Próxima Actividad -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-info">
                                    <h6 class="m-0 font-weight-bold text-white">
                                        <i class="fas fa-calendar-alt"></i> Próxima Actividad
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php if ($proxima_actividad): 
                                        $color = crudClientes::getColorActividad($proxima_actividad['fecha'], $proxima_actividad['completada']);
                                        $fecha_formato = date('d/m/Y', strtotime($proxima_actividad['fecha']));
                                    ?>
                                        <div class="alert alert-<?php 
                                            echo $color === 'hoy' ? 'success' : ($color === 'pasada' ? 'danger' : 'secondary'); 
                                        ?>" role="alert">
                                            <strong><?php echo $proxima_actividad['tipo']; ?></strong>
                                            <br>
                                            <small><?php echo $fecha_formato; ?></small>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">No hay actividades próximas</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>

                        <!-- Sidebar Derecho -->
                        <div class="col-lg-4">
                            <!-- Botones de Acción -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Acciones</h6>
                                </div>
                                <div class="card-body">
                                    <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-warning btn-block mb-2">
                                        <i class="fas fa-edit"></i> Editar Cliente
                                    </a>
                                    <a href="clientes.php" class="btn btn-secondary btn-block">
                                        <i class="fas fa-list"></i> Volver a Clientes
                                    </a>
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <div class="text-primary text-uppercase mb-1">Total Actividades</div>
                                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo $actividades->num_rows; ?></div>
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <div class="text-success text-uppercase mb-1">Última Visita</div>
                                        <div class="small">
                                            <?php 
                                            if ($cliente['ultima_visita']) {
                                                echo date('d/m/Y', strtotime($cliente['ultima_visita']));
                                            } else {
                                                echo 'Sin visitas';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de Actividades -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-success">
                            <h6 class="m-0 font-weight-bold text-white">
                                <i class="fas fa-list"></i> Historial de Actividades
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if ($actividades->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Descripción</th>
                                                <th>Fecha</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $actividades->data_seek(0);
                                            while ($act = $actividades->fetch_assoc()): 
                                                $color = crudClientes::getColorActividad($act['fecha'], $act['completada']);
                                                $fecha_formato = date('d/m/Y', strtotime($act['fecha']));
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo $act['tipo']; ?></strong>
                                                </td>
                                                <td>
                                                    <?php echo !empty($act['descripcion']) ? htmlspecialchars($act['descripcion']) : '<span class="text-muted">-</span>'; ?>
                                                </td>
                                                <td><?php echo $fecha_formato; ?></td>
                                                <td>
                                                    <span class="badge badge-<?php 
                                                        echo $act['completada'] == 1 ? 'success' : ($color === 'pasada' ? 'danger' : 'warning');
                                                    ?>">
                                                        <?php echo $act['completada'] == 1 ? '✓ Completada' : '⏳ Pendiente'; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No hay actividades registradas</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; PhoneCRM 2025</span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <script src="../startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js"></script>
    <script src="../startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
