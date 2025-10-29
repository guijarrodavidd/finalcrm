<?php
session_start();
include("./admin/includes/database.php");

$connClass = new Connection();
$conexion = $connClass->getConnection();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$sql_total = "SELECT COUNT(*) as total FROM clientes WHERE usuario_id = $usuario_id";
$resultado_total = mysqli_query($conexion, $sql_total);
$total_clientes = mysqli_fetch_assoc($resultado_total)['total'];

$sql = "SELECT c.id, c.nombre_apellidos, c.dni, c.telefono, c.convergente, c.fecha_creacion 
        FROM clientes c 
        WHERE c.usuario_id = $usuario_id 
        ORDER BY c.fecha_creacion DESC";
$resultado = mysqli_query($conexion, $sql);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar_actividad'])) {
    $cliente_id = intval($_POST['cliente_id']);
    $tipo = $_POST['tipo'];
    $descripcion = trim($_POST['descripcion']);
    $fecha = $_POST['fecha'];
    
    if (!empty($cliente_id) && !empty($tipo) && !empty($fecha)) {
        $sql_insert = "INSERT INTO actividades (cliente_id, tipo, descripcion, fecha, completada) VALUES (?, ?, ?, ?, 0)";
        $stmt = mysqli_prepare($conexion, $sql_insert);
        mysqli_stmt_bind_param($stmt, "isss", $cliente_id, $tipo, $descripcion, $fecha);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        header("Location: clientes.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['marcar_completada'])) {
    $actividad_id = intval($_POST['actividad_id']);
    $estado = intval($_POST['estado']);
    
    $sql_update = "UPDATE actividades SET completada = ? WHERE id = ?";
    $stmt = mysqli_prepare($conexion, $sql_update);
    mysqli_stmt_bind_param($stmt, "ii", $estado, $actividad_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header("Location: clientes.php");
    exit();
}

function getColorActividad($fecha, $completada) {
    if ($completada) {
        return 'completada';
    }
    
    $hoy = date('Y-m-d');
    $fecha_actividad = date('Y-m-d', strtotime($fecha));
    
    if ($fecha_actividad < $hoy) {
        return 'pasada';
    } elseif ($fecha_actividad == $hoy) {
        return 'hoy';
    } else {
        return 'futura';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PhoneCRM - Clientes</title>
    <link href="startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .badge-etiqueta {
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .row-actividades {
            background-color: #f8f9fa;
        }

        .cliente-principal {
            cursor: pointer;
        }

        .cliente-principal:hover {
            background-color: #e8f4ff;
        }

        .expandible-row {
            display: none;
        }

        .expandible-row.show {
            display: table-row;
        }

        .actividad-item {
            background: white;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 4px;
            border-left: 4px solid;
            display: flex;
            justify-content: space-between;
            align-items: start;
        }

        .actividad-contenido {
            flex: 1;
        }

        .actividad-checkbox {
            margin-left: 10px;
        }

        .actividad-futura {
            border-left-color: #6c757d !important;
            background-color: #f8f9fa;
            color: #6c757d;
        }
        .actividad-hoy {
            border-left-color: #28a745 !important;
            background-color: #f0fdf4;
            color: #155724;
        }
        .actividad-pasada {
            border-left-color: #dc3545 !important;
            background-color: #fdf2f2;
            color: #721c24;
        }
        .actividad-completada {
            border-left-color: #6c757d !important;
            background-color: #e9ecef;
            color: #6c757d;
            opacity: 0.6;
            text-decoration: line-through;
        }

        .proxima-futura {
            background-color: #6c757d;
            color: white;
        }
        .proxima-hoy {
            background-color: #28a745;
            color: white;
        }
        .proxima-pasada {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body id="page-top">

    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="principal.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="sidebar-brand-text mx-3">PhoneCRM</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="principal.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">Gestión</div>

            <li class="nav-item active">
                <a class="nav-link" href="clientes.php">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Clientes</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="anadir_cliente.php">
                    <i class="fas fa-fw fa-user-plus"></i>
                    <span>Añadir Cliente</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="ventas.php">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>Ventas</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="actividades.php">
                    <i class="fas fa-fw fa-tasks"></i>
                    <span>Actividades</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                                <i class="fas fa-user-circle fa-2x"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Clientes</h1>
                    <p class="mb-4">Listado de todos tus clientes. Total: <strong><?php echo $total_clientes; ?></strong></p>

                    <!-- Leyenda de colores -->
                    <div class="alert alert-info mb-4">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            <span class="badge badge-secondary">GRIS</span> Futuro
                            <span class="badge badge-success ml-2">VERDE</span> Hoy
                            <span class="badge badge-danger ml-2">ROJO</span> Pasado
                            <span class="badge badge-secondary ml-2">✓ ATENUADO</span> Completada
                            | Haz click en un cliente para ver sus actividades
                        </small>
                    </div>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">Tabla de Clientes</h6>
                                </div>
                                <div class="col text-right">
                                    <a href="anadir_cliente.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nuevo Cliente
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($total_clientes == 0): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-gray-500 mb-3">No hay clientes registrados aún</p>
                                    <a href="anadir_cliente.php" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Añadir Primer Cliente
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="20%">Nombre</th>
                                                <th width="12%">Teléfono</th>
                                                <th width="15%">Próxima Actividad</th>
                                                <th width="20%">Etiquetas</th>
                                                <th width="15%">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($cliente = mysqli_fetch_assoc($resultado)): ?>
                                                <!-- Fila principal del cliente -->
                                                <tr class="cliente-principal" onclick="toggleRow(this)">
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($cliente['nombre_apellidos']); ?></strong>
                                                        <br>
                                                        <small class="text-muted">ID: <?php echo $cliente['id']; ?> | DNI: <?php echo htmlspecialchars($cliente['dni']); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                                                    <td>
                                                        <?php
                                                        $sql_proxima = "SELECT id, tipo, fecha, completada FROM actividades 
                                                                       WHERE cliente_id = " . $cliente['id'] . " 
                                                                       AND completada = 0
                                                                       ORDER BY fecha ASC LIMIT 1";
                                                        $resultado_proxima = mysqli_query($conexion, $sql_proxima);
                                                        $actividad = mysqli_fetch_assoc($resultado_proxima);
                                                        
                                                        if ($actividad):
                                                            $fecha_format = date('d/m/Y', strtotime($actividad['fecha']));
                                                            $tipo = $actividad['tipo'];
                                                            $color_fecha = getColorActividad($actividad['fecha'], 0);
                                                        ?>
                                                            <span class="badge proxima-<?php echo $color_fecha; ?>">
                                                                <?php echo $tipo; ?>
                                                            </span>
                                                            <small class="d-block"><?php echo $fecha_format; ?></small>
                                                        <?php else: ?>
                                                            <small class="text-muted">Sin actividades pendientes</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $sql_etiquetas = "SELECT e.nombre, e.color FROM etiquetas e 
                                                                         JOIN cliente_etiqueta ce ON e.id = ce.etiqueta_id 
                                                                         WHERE ce.cliente_id = " . $cliente['id'];
                                                        $resultado_etiquetas = mysqli_query($conexion, $sql_etiquetas);
                                                        
                                                        if (mysqli_num_rows($resultado_etiquetas) > 0):
                                                            while ($etiqueta = mysqli_fetch_assoc($resultado_etiquetas)):
                                                        ?>
                                                                <span class="badge-etiqueta" style="background-color: <?php echo htmlspecialchars($etiqueta['color']); ?>33; color: <?php echo htmlspecialchars($etiqueta['color']); ?>; border: 1px solid <?php echo htmlspecialchars($etiqueta['color']); ?>;">
                                                                    <?php echo htmlspecialchars($etiqueta['nombre']); ?>
                                                                </span>
                                                        <?php
                                                            endwhile;
                                                        else:
                                                        ?>
                                                            <small class="text-muted">-</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td onclick="event.stopPropagation();">
                                                        <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="if(confirm('¿Estás seguro?')) window.location='eliminar_cliente.php?id=<?php echo $cliente['id']; ?>'" title="Eliminar">
                                                            <i class="fas fa-trash"></i> Eliminar
                                                        </a>
                                                    </td>
                                                </tr>

                                                <!-- Fila expandible con actividades -->
                                                <tr class="expandible-row row-actividades" data-cliente="<?php echo $cliente['id']; ?>">
                                                    <td colspan="5">
                                                        <div class="p-4">
                                                            <div class="row">
                                                                <!-- Actividades existentes -->
                                                                <div class="col-md-6">
                                                                    <h6 class="mb-3"><i class="fas fa-list"></i> Actividades del Cliente</h6>
                                                                    <?php
                                                                    $sql_acts = "SELECT id, tipo, descripcion, fecha, completada FROM actividades 
                                                                                 WHERE cliente_id = " . $cliente['id'] . " 
                                                                                 ORDER BY fecha DESC LIMIT 10";
                                                                    $resultado_acts = mysqli_query($conexion, $sql_acts);
                                                                    
                                                                    if (mysqli_num_rows($resultado_acts) > 0):
                                                                        while ($act = mysqli_fetch_assoc($resultado_acts)):
                                                                            $color_fecha = getColorActividad($act['fecha'], $act['completada']);
                                                                    ?>
                                                                        <div class="actividad-item actividad-<?php echo $color_fecha; ?>">
                                                                            <div class="actividad-contenido">
                                                                                <strong><?php echo $act['tipo']; ?></strong>
                                                                                <small class="ml-2"><?php echo date('d/m/Y', strtotime($act['fecha'])); ?></small>
                                                                                <?php if (!empty($act['descripcion'])): ?>
                                                                                    <p class="mb-0 mt-2"><?php echo htmlspecialchars($act['descripcion']); ?></p>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                            <form method="POST" class="actividad-checkbox" onclick="event.stopPropagation();">
                                                                                <input type="hidden" name="actividad_id" value="<?php echo $act['id']; ?>">
                                                                                <input type="hidden" name="estado" value="<?php echo $act['completada'] == 1 ? 0 : 1; ?>">
                                                                                <button type="submit" name="marcar_completada" class="btn btn-sm <?php echo $act['completada'] == 1 ? 'btn-success' : 'btn-outline-success'; ?>" title="Marcar como completada">
                                                                                    <i class="fas fa-check"></i> <?php echo $act['completada'] == 1 ? 'Desmarcar' : 'Marcar como completada'; ?>
                                                                                </button>

                                                                            </form>
                                                                        </div>
                                                                    <?php
                                                                        endwhile;
                                                                    else:
                                                                    ?>
                                                                        <p class="text-muted">Sin actividades registradas</p>
                                                                    <?php endif; ?>
                                                                </div>

                                                                <!-- Formulario agregar actividad -->
                                                                <div class="col-md-6">
                                                                    <h6 class="mb-3"><i class="fas fa-calendar-plus"></i> Agendar Nueva Actividad</h6>
                                                                    <form method="POST" style="font-size: 0.95rem;" onclick="event.stopPropagation();">
                                                                        <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                                                                        
                                                                        <div class="form-group mb-2">
                                                                            <label class="mb-1">Tipo de Actividad</label>
                                                                            <select name="tipo" class="form-control form-control-sm" required>
                                                                                <option value="">-- Selecciona --</option>
                                                                                <option value="Llamada">📞 Llamada</option>
                                                                                <option value="WhatsApp">💬 WhatsApp</option>
                                                                                <option value="Cita">📅 Cita</option>
                                                                                <option value="Revisión">🔍 Revisión</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="form-group mb-2">
                                                                            <label class="mb-1">Fecha</label>
                                                                            <input type="date" name="fecha" class="form-control form-control-sm" required>
                                                                        </div>

                                                                        <div class="form-group mb-2">
                                                                            <label class="mb-1">Descripción (opcional)</label>
                                                                            <textarea name="descripcion" class="form-control form-control-sm" rows="2" placeholder="Observaciones..."></textarea>
                                                                        </div>

                                                                        <button type="submit" name="agregar_actividad" class="btn btn-primary btn-sm btn-block">
                                                                            <i class="fas fa-save"></i> Agendar
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; PhoneCRM 2025</span>
                    </div>
                </div>
            </footer>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js"></script>
    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="startbootstrap-sb-admin-2-gh-pages/js/sb-admin-2.min.js"></script>

    <script>
        function toggleRow(element) {
            var allRows = document.querySelectorAll('.expandible-row');
            var nextRow = element.nextElementSibling;
            
            allRows.forEach(function(row) {
                if (row !== nextRow) {
                    row.classList.remove('show');
                }
            });
            
            nextRow.classList.toggle('show');
        }
    </script>
</body>
</html>
