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

// Obtener ID del cliente
$cliente_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($cliente_id == 0) {
    header("Location: clientes.php");
    exit();
}

// Obtener datos del cliente
$sql = "SELECT * FROM clientes WHERE id = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();

if (!$cliente) {
    header("Location: clientes.php");
    exit();
}

// Procesar agregar actividad desde ver_cliente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar_actividad_cliente'])) {
    $tipo = trim($_POST['tipo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha = $_POST['fecha'];
    
    if (!empty($tipo) && !empty($fecha)) {
        $crudAct->agregarActividad($cliente_id, $tipo, $descripcion, $fecha);
        $crud->actualizarUltimaVisita($cliente_id);
        header("Location: clientes.php");
        exit();
    }
}

// Obtener actividades del cliente
$actividades = $crudAct->getActividades($cliente_id);
$etiquetas = $crud->getEtiquetas($cliente_id);
$proxima_actividad = $crudAct->getProximaActividad($cliente_id);
$hoy = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PhoneCRM - Ver Cliente</title>
    <link href="startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css" rel="stylesheet">
<<<<<<< HEAD
    <link rel="icon" type="image/png" href="images/favicon.png">

=======
    <link href="css/main.css" rel="stylesheet">
>>>>>>> ef79cdbb74705fff1ead2c1032e2969cd320a08a
</head>
<body id="page-top">

    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("./admin/includes/navbar.php"); ?>
                <div class="container-fluid">

                    <!-- Botón volver -->
                    <div class="mb-3 mt-4">
                        <a href="clientes.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver a Clientes
                        </a>
                    </div>

                    <!-- Card principal del cliente -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="m-0 font-weight-bold">
                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($cliente['nombre_apellidos']); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-phone"></i> Teléfono:</strong></p>
                                    <p><a href="tel:<?php echo htmlspecialchars($cliente['telefono']); ?>"><?php echo htmlspecialchars($cliente['telefono']); ?></a></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-id-card"></i> DNI:</strong></p>
                                    <p><?php echo htmlspecialchars($cliente['dni']); ?></p>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-wifi"></i> Convergente:</strong></p>
                                    <p><?php echo !empty($cliente['convergente']) ? htmlspecialchars($cliente['convergente']) : 'Sin convergente'; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-calendar"></i> Cliente Desde:</strong></p>
                                    <p><?php echo date('d/m/Y', strtotime($cliente['fecha_creacion'])); ?></p>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-clock"></i> Última Visita:</strong></p>
                                    <p><?php echo $cliente['ultima_visita'] ? date('d/m/Y H:i', strtotime($cliente['ultima_visita'])) : 'Sin visitas'; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-tags"></i> Etiquetas:</strong></p>
                                    <p>
                                        <?php 
                                        if ($etiquetas->num_rows > 0):
                                            while ($etiqueta = $etiquetas->fetch_assoc()):
                                        ?>
                                            <span class="badge" style="background-color: <?php echo htmlspecialchars($etiqueta['color']); ?>; color: white; padding: 0.4rem 0.8rem; margin-right: 0.3rem;">
                                                <?php echo htmlspecialchars($etiqueta['nombre']); ?>
                                            </span>
                                        <?php
                                            endwhile;
                                        else:
                                        ?>
                                            <span class="text-muted">Sin etiquetas</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Próxima Actividad -->
                    <?php if ($proxima_actividad): 
                        $color = crudClientes::getColorActividad($proxima_actividad['fecha'], $proxima_actividad['completada']);
                        $fecha_formato = date('d/m/Y', strtotime($proxima_actividad['fecha']));
                    ?>
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white py-3">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-bell"></i> Próxima Actividad
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0"><strong><?php echo $proxima_actividad['tipo']; ?></strong> - <?php echo $fecha_formato; ?></p>
                            <?php if (!empty($proxima_actividad['descripcion'])): ?>
                                <p class="text-muted mb-0 mt-2"><?php echo htmlspecialchars($proxima_actividad['descripcion']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Formulario para agregar actividad -->
                    <div class="card shadow mb-4 border-left-primary">
                        <div class="card-header bg-gradient-primary py-3">
                            <h6 class="m-0 font-weight-bold text-white">
                                <i class="fas fa-plus-circle"></i> Agendar Nueva Actividad
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" id="formActividad">
                                <!-- Selector de tipo de actividad -->
                                <div class="form-group">
                                    <label class="font-weight-bold mb-3">
                                        <i class="fas fa-list"></i> Selecciona el tipo de actividad:
                                    </label>
                                    <div class="btn-group btn-group-toggle d-flex flex-wrap" data-toggle="buttons" style="width: 100%;">
                                        <label class="btn btn-outline-primary m-2 flex-grow-1" style="min-width: 120px;">
                                            <input type="radio" name="tipo" value="Llamada" autocomplete="off">
                                            <i class="fas fa-phone"></i> Llamada
                                        </label>
                                        <label class="btn btn-outline-success m-2 flex-grow-1" style="min-width: 120px;">
                                            <input type="radio" name="tipo" value="WhatsApp" autocomplete="off">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </label>
                                        <label class="btn btn-outline-info m-2 flex-grow-1" style="min-width: 120px;">
                                            <input type="radio" name="tipo" value="Cita" autocomplete="off">
                                            <i class="fas fa-calendar"></i> Cita
                                        </label>
                                        <label class="btn btn-outline-warning m-2 flex-grow-1" style="min-width: 120px;">
                                            <input type="radio" name="tipo" value="Revisión" autocomplete="off">
                                            <i class="fas fa-microscope"></i> Revisión
                                        </label>
                                    </div>
                                    <small class="form-text text-muted d-block mt-2">Haz clic en el tipo de actividad que deseas agendar</small>
                                </div>

                                <hr>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="fecha_actividad" class="font-weight-bold">
                                            <i class="fas fa-calendar-alt"></i> Fecha de la Actividad
                                        </label>
                                        <input type="date" class="form-control form-control-lg" id="fecha_actividad" name="fecha" required>
                                        <small class="form-text text-muted">Selecciona una fecha para la actividad</small>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="hora_actividad" class="font-weight-bold">
                                            <i class="fas fa-clock"></i> Hora (Opcional)
                                        </label>
                                        <input type="time" class="form-control form-control-lg" id="hora_actividad" name="hora">
                                        <small class="form-text text-muted">Hora sugerida para la actividad</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="descripcion_actividad" class="font-weight-bold">
                                        <i class="fas fa-sticky-note"></i> Descripción (Opcional)
                                    </label>
                                    <textarea class="form-control form-control-lg" id="descripcion_actividad" name="descripcion" rows="3" placeholder="Añade detalles sobre la actividad, notas importantes, motivo de la llamada, etc..."></textarea>
                                    <small class="form-text text-muted">Máximo 500 caracteres</small>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" name="agregar_actividad_cliente" class="btn btn-primary btn-lg btn-block font-weight-bold" id="btnAgendar">
                                        <i class="fas fa-plus"></i> Agendar Actividad
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Historial de Actividades -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-success text-white py-3">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-history"></i> Historial de Actividades
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php
                            $actividades->data_seek(0);
                            if ($actividades->num_rows > 0):
                            ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="15%">Tipo</th>
                                                <th width="30%">Descripción</th>
                                                <th width="15%">Fecha</th>
                                                <th width="15%">Estado</th>
                                                <th width="25%">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            while ($act = $actividades->fetch_assoc()): 
                                                $color = crudClientes::getColorActividad($act['fecha'], $act['completada']);
                                                $fecha_formato = date('d/m/Y', strtotime($act['fecha']));
                                                $fecha_actividad = date('Y-m-d', strtotime($act['fecha']));
                                            ?>
                                            <tr class="<?php echo ($act['completada'] == 1) ? 'actividad-completada-row' : ''; ?>">
                                                <td><strong><?php echo $act['tipo']; ?></strong></td>
                                                <td><?php echo !empty($act['descripcion']) ? htmlspecialchars($act['descripcion']) : '<span class="text-muted">-</span>'; ?></td>
                                                <td>
                                                    <span class="badge badge-<?php 
                                                        if ($act['completada']) {
                                                            echo 'secondary';
                                                        } elseif ($fecha_actividad < $hoy) {
                                                            echo 'danger';
                                                        } elseif ($fecha_actividad == $hoy) {
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
                                                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Completada</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning"><i class="fas fa-hourglass-half"></i> Pendiente</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <form method="POST" action="actividades.php" style="display: inline;">
                                                        <input type="hidden" name="actividad_id" value="<?php echo $act['id']; ?>">
                                                        <input type="hidden" name="estado" value="<?php echo $act['completada'] == 1 ? 0 : 1; ?>">
                                                        <button type="submit" name="marcar_completada" class="btn btn-sm <?php echo $act['completada'] == 1 ? 'btn-warning' : 'btn-success'; ?>">
                                                            <i class="fas fa-<?php echo $act['completada'] == 1 ? 'undo' : 'check'; ?>"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center py-4">
                                    <i class="fas fa-inbox fa-2x mb-3"></i>
                                    <br>Sin actividades registradas
                                </p>
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

    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js"></script>
    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        // Validar que se seleccione un tipo antes de enviar
        document.getElementById('formActividad').addEventListener('submit', function(e) {
            const tipoSeleccionado = document.querySelector('input[name="tipo"]:checked');
            
            if (!tipoSeleccionado) {
                e.preventDefault();
                alert('Por favor selecciona un tipo de actividad');
                return false;
            }
        });

        // Mantener el botón marcado cuando se hace click
        document.querySelectorAll('input[name="tipo"]').forEach(input => {
            input.addEventListener('change', function() {
                document.querySelectorAll('.btn-group label').forEach(label => {
                    label.classList.remove('active');
                });
                this.parentElement.classList.add('active');
            });
        });
    </script>

</body>
</html>
