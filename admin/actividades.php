<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("./includes/database.php");
require_once "./includes/crudClientes.php";
require_once "./includes/crudActividades.php";

$connClass = new Connection();
$conexion = $connClass->getConnection();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$sql_rol = "SELECT rol FROM usuarios WHERE id = ?";
$stmt_rol = $conexion->prepare($sql_rol);
$stmt_rol->bind_param("i", $usuario_id);
$stmt_rol->execute();
$resultado_rol = $stmt_rol->get_result()->fetch_assoc();

if ($resultado_rol['rol'] !== 'encargado') {
    header("Location: ../index.php");
    exit();
}

$crud = new crudClientes($conexion, $usuario_id);
$crudAct = new crudActividades($conexion, $usuario_id);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['marcar_completada'])) {
    $actividad_id = intval($_POST['actividad_id']);
    $estado = intval($_POST['estado']);
    $crudAct->marcarCompletada($actividad_id, $estado);
}

$filtro = $_GET['filtro'] ?? 'todas';
$tienda_filtro = $_GET['tienda'] ?? null;
$nombre_filtro = $_GET['nombre'] ?? null;
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$por_pagina = 30;

$sql_tiendas = "SELECT id, nombre FROM usuarios WHERE rol = 'tienda' ORDER BY nombre ASC";
$todas_tiendas = $conexion->query($sql_tiendas);

$total_paginas = 1;
$actividades = null;
$hoy = date('Y-m-d');

$where = "WHERE 1=1";
if ($filtro === 'pendientes') {
    $where .= " AND a.completada = 0";
} elseif ($filtro === 'completadas') {
    $where .= " AND a.completada = 1";
}

if ($tienda_filtro) {
    $tienda_filtro = intval($tienda_filtro);
    $where .= " AND c.usuario_id = " . $tienda_filtro;
}

if ($nombre_filtro) {
    $nombre_filtro = $conexion->real_escape_string($nombre_filtro);
    $where .= " AND c.nombre_apellidos LIKE '%" . $nombre_filtro . "%'";
}

$sql_count = "SELECT COUNT(*) as total FROM actividades a JOIN clientes c ON a.cliente_id = c.id JOIN usuarios u ON c.usuario_id = u.id " . $where;
$result_count = $conexion->query($sql_count);
$total = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total / $por_pagina);

if ($pagina < 1) $pagina = 1;
if ($pagina > $total_paginas && $total_paginas > 0) $pagina = $total_paginas;

$offset = ($pagina - 1) * $por_pagina;
$sql = "SELECT a.id, a.cliente_id, a.tipo, a.descripcion, a.fecha, a.completada, c.nombre_apellidos, c.telefono, c.convergente, c.usuario_id, u.nombre as tienda_nombre FROM actividades a JOIN clientes c ON a.cliente_id = c.id JOIN usuarios u ON c.usuario_id = u.id " . $where . " ORDER BY a.fecha DESC LIMIT " . $por_pagina . " OFFSET " . $offset;

$actividades = $conexion->query($sql);
$total_actividades = $actividades->num_rows;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PhoneCRM - Actividades (Admin)</title>
    <link href="./vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("./includes/navbar.php"); ?>
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
                        <h1 class="h3 mb-0 text-gray-800">Actividades (Admin - Todas las Tiendas)</h1>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label><strong>Estado</strong></label>
                                    <div class="btn-group btn-block" role="group">
                                        <a href="actividades.php?filtro=todas&pagina=1<?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?><?php echo $nombre_filtro ? '&nombre=' . urlencode($nombre_filtro) : ''; ?>" class="btn btn-outline-primary <?php echo $filtro === 'todas' ? 'active' : ''; ?>" style="width: 33.33%;"><i class="fas fa-list"></i> Todas</a>
                                        <a href="actividades.php?filtro=pendientes&pagina=1<?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?><?php echo $nombre_filtro ? '&nombre=' . urlencode($nombre_filtro) : ''; ?>" class="btn btn-outline-warning <?php echo $filtro === 'pendientes' ? 'active' : ''; ?>" style="width: 33.33%;"><i class="fas fa-hourglass-half"></i> Pendientes</a>
                                        <a href="actividades.php?filtro=completadas&pagina=1<?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?><?php echo $nombre_filtro ? '&nombre=' . urlencode($nombre_filtro) : ''; ?>" class="btn btn-outline-success <?php echo $filtro === 'completadas' ? 'active' : ''; ?>" style="width: 33.33%;"><i class="fas fa-check-circle"></i> Completadas</a>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label><strong>Tienda</strong></label>
                                    <form method="GET" style="width: 100%;">
                                        <input type="hidden" name="filtro" value="<?php echo $filtro; ?>">
                                        <input type="hidden" name="pagina" value="1">
                                        <select name="tienda" class="form-control" style="width: 100%;" onchange="this.form.submit();">
                                            <option value="">-- Todas las tiendas --</option>
                                            <?php 
                                            if ($todas_tiendas->num_rows > 0):
                                                while ($tienda = $todas_tiendas->fetch_assoc()):
                                                    $is_selected = $tienda_filtro == $tienda['id'];
                                            ?>
                                                <option value="<?php echo $tienda['id']; ?>" <?php echo $is_selected ? 'selected' : ''; ?>><?php echo htmlspecialchars($tienda['nombre']); ?></option>
                                            <?php endwhile; endif; ?>
                                        </select>
                                        <?php if ($nombre_filtro): ?><input type="hidden" name="nombre" value="<?php echo urlencode($nombre_filtro); ?>"><?php endif; ?>
                                    </form>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label><strong>Buscar Cliente</strong></label>
                                    <form method="GET" style="width: 100%;">
                                        <input type="hidden" name="filtro" value="<?php echo $filtro; ?>">
                                        <input type="hidden" name="pagina" value="1">
                                        <div class="input-group">
                                            <input type="text" name="nombre" class="form-control" placeholder="Nombre cliente..." value="<?php echo htmlspecialchars($nombre_filtro); ?>">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                        <?php if ($tienda_filtro): ?><input type="hidden" name="tienda" value="<?php echo $tienda_filtro; ?>"><?php endif; ?>
                                    </form>
                                </div>
                            </div>
                            <?php if (!empty($nombre_filtro) || $tienda_filtro): ?>
                            <div class="mt-2">
                                <a href="actividades.php?filtro=<?php echo $filtro; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Limpiar filtros</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-primary">
                            <h6 class="m-0 font-weight-bold text-white">Todas las Actividades <?php if ($tienda_filtro || !empty($nombre_filtro)) echo "(Filtradas)"; ?></h6>
                        </div>
                        <div class="card-body">
                            <?php if ($total_actividades == 0): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-tasks fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-gray-500 mb-3">No hay actividades que coincidan con los filtros</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="12%">Tienda</th>
                                                <th width="15%">Cliente</th>
                                                <th width="10%">Teléfono</th>
                                                <th width="12%">Tipo</th>
                                                <th width="20%">Descripción</th>
                                                <th width="10%">Fecha</th>
                                                <th width="10%">Estado</th>
                                                <th width="11%">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($actividad = $actividades->fetch_assoc()): 
                                                $fecha_formateada = date('d/m/Y', strtotime($actividad['fecha']));
                                                $fecha_actividad = date('Y-m-d', strtotime($actividad['fecha']));
                                            ?>
                                            <tr class="<?php echo ($actividad['completada'] == 1) ? 'actividad-completada-row' : ''; ?>">
                                                <td><strong><?php echo htmlspecialchars($actividad['tienda_nombre']); ?></strong></td>
                                                <td><a href="ver_cliente.php?id=<?php echo $actividad['cliente_id']; ?>" style="text-decoration: none;"><strong><?php echo htmlspecialchars($actividad['nombre_apellidos']); ?></strong></a></td>
                                                <td><?php echo htmlspecialchars($actividad['telefono']); ?></td>
                                                <td><?php echo $actividad['tipo']; ?></td>
                                                <td><?php if (!empty($actividad['descripcion'])): echo htmlspecialchars($actividad['descripcion']); else: ?><span class="text-muted">-</span><?php endif; ?></td>
                                                <td><span class="badge badge-<?php if ($actividad['completada']) { echo 'secondary'; } elseif ($fecha_actividad < $hoy) { echo 'danger'; } elseif ($fecha_actividad == $hoy) { echo 'success'; } else { echo 'info'; } ?>"><?php echo $fecha_formateada; ?></span></td>
                                                <td><?php if ($actividad['completada'] == 1): ?><span class="badge badge-success"><i class="fas fa-check-circle"></i> Completada</span><?php else: ?><span class="badge badge-warning"><i class="fas fa-hourglass-half"></i> Pendiente</span><?php endif; ?></td>
                                                <td><form method="POST" style="display: inline;"><input type="hidden" name="actividad_id" value="<?php echo $actividad['id']; ?>"><input type="hidden" name="estado" value="<?php echo $actividad['completada'] == 1 ? 0 : 1; ?>"><button type="submit" name="marcar_completada" class="btn btn-sm <?php echo $actividad['completada'] == 1 ? 'btn-warning' : 'btn-success'; ?>"><i class="fas fa-<?php echo $actividad['completada'] == 1 ? 'undo' : 'check'; ?>"></i> <?php echo $actividad['completada'] == 1 ? 'Desmarcar' : 'Completar'; ?></button></form></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <?php if ($total_paginas > 1): ?>
                                <nav aria-label="Paginación" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($pagina > 1): ?>
                                            <li class="page-item"><a class="page-link" href="actividades.php?filtro=<?php echo $filtro; ?>&pagina=1<?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?><?php echo $nombre_filtro ? '&nombre=' . urlencode($nombre_filtro) : ''; ?>"><i class="fas fa-chevron-left"></i> Primera</a></li>
                                            <li class="page-item"><a class="page-link" href="actividades.php?filtro=<?php echo $filtro; ?>&pagina=<?php echo $pagina - 1; ?><?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?><?php echo $nombre_filtro ? '&nombre=' . urlencode($nombre_filtro) : ''; ?>">Anterior</a></li>
                                        <?php endif; ?>
                                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                            <?php if ($i == $pagina): ?>
                                                <li class="page-item active"><span class="page-link"><?php echo $i; ?></span></li>
                                            <?php elseif ($i >= $pagina - 2 && $i <= $pagina + 2): ?>
                                                <li class="page-item"><a class="page-link" href="actividades.php?filtro=<?php echo $filtro; ?>&pagina=<?php echo $i; ?><?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?><?php echo $nombre_filtro ? '&nombre=' . urlencode($nombre_filtro) : ''; ?>"><?php echo $i; ?></a></li>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <?php if ($pagina < $total_paginas): ?>
                                            <li class="page-item"><a class="page-link" href="actividades.php?filtro=<?php echo $filtro; ?>&pagina=<?php echo $pagina + 1; ?><?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?><?php echo $nombre_filtro ? '&nombre=' . urlencode($nombre_filtro) : ''; ?>">Siguiente</a></li>
                                            <li class="page-item"><a class="page-link" href="actividades.php?filtro=<?php echo $filtro; ?>&pagina=<?php echo $total_paginas; ?><?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?><?php echo $nombre_filtro ? '&nombre=' . urlencode($nombre_filtro) : ''; ?>">Última <i class="fas fa-chevron-right"></i></a></li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                                <?php endif; ?>
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
    <script src="./vendor/jquery/jquery.min.js"></script>
    <script src="./vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
