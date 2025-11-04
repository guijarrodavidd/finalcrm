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

// Marcar completada/incompleta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['marcar_completada'])) {
    $actividad_id = intval($_POST['actividad_id']);
    $estado = intval($_POST['estado']);
    
    $crudAct->marcarCompletada($actividad_id, $estado);
}

// Obtener filtro y paginación
$filtro = $_GET['filtro'] ?? 'todas';
$etiqueta_filtro = $_GET['etiqueta'] ?? null;
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$por_pagina = 30;

// Obtener TODAS las etiquetas del sistema
$todas_etiquetas = $crudAct->getTodasEtiquetasSistema();

// Calcular páginas según filtro
if ($etiqueta_filtro) {
    $etiqueta_filtro = intval($etiqueta_filtro);
    $total_paginas = $crudAct->getTotalPaginasActividadesEtiqueta($etiqueta_filtro, $filtro, $por_pagina);
} else {
    if ($filtro === 'pendientes') {
        $total_paginas = $crudAct->getTotalPaginasActividadesPendientes($por_pagina);
    } elseif ($filtro === 'completadas') {
        $total_paginas = $crudAct->getTotalPaginasActividadesCompletadas($por_pagina);
    } else {
        $total_paginas = $crudAct->getTotalPaginasActividades($por_pagina);
    }
}

if ($pagina < 1) $pagina = 1;
if ($pagina > $total_paginas && $total_paginas > 0) $pagina = $total_paginas;

// Obtener actividades según filtro
if ($etiqueta_filtro) {
    $actividades = $crudAct->getActividadesPaginadasPorEtiqueta($etiqueta_filtro, $filtro, $pagina, $por_pagina);
} else {
    if ($filtro === 'pendientes') {
        $actividades = $crudAct->getActividadesPendientesPaginadas($pagina, $por_pagina);
    } elseif ($filtro === 'completadas') {
        $actividades = $crudAct->getActividadesCompletadasPaginadas($pagina, $por_pagina);
    } else {
        $actividades = $crudAct->getActividadesPaginadas($pagina, $por_pagina);
    }
}

$total_actividades = $actividades->num_rows;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PhoneCRM - Actividades</title>
    <link href="startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
</head>
<body id="page-top">

    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("./admin/includes/navbar.php"); ?>
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
                        <h1 class="h3 mb-0 text-gray-800">Actividades</h1>
                    </div>

                    <!-- Filtros de Estado -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Filtros de Estado</h6>
                        </div>
                        <div class="card-body">
                            <div class="btn-group btn-block" role="group">
                                <a href="actividades.php?filtro=todas&pagina=1<?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>" class="btn btn-outline-primary <?php echo $filtro === 'todas' ? 'active' : ''; ?>">
                                    <i class="fas fa-list"></i> Todas
                                </a>
                                <a href="actividades.php?filtro=pendientes&pagina=1<?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>" class="btn btn-outline-warning <?php echo $filtro === 'pendientes' ? 'active' : ''; ?>">
                                    <i class="fas fa-hourglass-half"></i> Pendientes
                                </a>
                                <a href="actividades.php?filtro=completadas&pagina=1<?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>" class="btn btn-outline-success <?php echo $filtro === 'completadas' ? 'active' : ''; ?>">
                                    <i class="fas fa-check-circle"></i> Completadas
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtro por Etiquetas -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Filtrar por Etiquetas</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="form-inline">
                                <input type="hidden" name="filtro" value="<?php echo $filtro; ?>">
                                <input type="hidden" name="pagina" value="1">
                                
                                <select name="etiqueta" class="form-control mr-2" onchange="this.form.submit();">
                                    <option value="">-- Todas las etiquetas --</option>
                                    <?php 
                                    $todas_etiquetas->data_seek(0);
                                    if ($todas_etiquetas->num_rows > 0):
                                        while ($etiqueta = $todas_etiquetas->fetch_assoc()):
                                            $is_selected = $etiqueta_filtro == $etiqueta['id'];
                                    ?>
                                        <option value="<?php echo $etiqueta['id']; ?>" <?php echo $is_selected ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($etiqueta['nombre']); ?>
                                        </option>
                                    <?php
                                        endwhile;
                                    endif;
                                    ?>
                                </select>
                                
                                <?php if ($etiqueta_filtro): ?>
                                    <a href="actividades.php?filtro=<?php echo $filtro; ?>&pagina=1" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-times"></i> Limpiar
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>

                    <!-- Tabla de Actividades -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-primary">
                            <h6 class="m-0 font-weight-bold text-white">
                                <?php 
                                    if ($etiqueta_filtro):
                                        echo "Actividades Filtradas";
                                    elseif ($filtro === 'pendientes'):
                                        echo "Actividades Pendientes";
                                    elseif ($filtro === 'completadas'):
                                        echo "Actividades Completadas";
                                    else:
                                        echo "Todas las Actividades";
                                    endif;
                                ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if ($total_actividades == 0): ?>
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>No se encuentran clientes con esta selección de etiquetas.</strong>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="15%">Cliente</th>
                                                <th width="10%">Convergente</th>
                                                <th width="20%">Etiquetas</th>
                                                <th width="12%">Tipo</th>
                                                <th width="20%">Descripción</th>
                                                <th width="10%">Fecha</th>
                                                <th width="10%">Estado</th>
                                                <th width="13%">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($actividad = $actividades->fetch_assoc()): 
                                                $fecha_formateada = date('d/m/Y', strtotime($actividad['fecha']));
                                                $color = crudClientes::getColorActividad($actividad['fecha'], $actividad['completada']);
                                                $hoy = date('Y-m-d');
                                                $fecha_actividad = date('Y-m-d', strtotime($actividad['fecha']));
                                            ?>
                                            <tr class="<?php echo $actividad['completada'] == 1 ? 'table-light' : ''; ?>">
                                                <td>
                                                    <a href="ver_cliente.php?id=<?php echo $actividad['cliente_id']; ?>" style="text-decoration: none;">
                                                        <strong><?php echo htmlspecialchars($actividad['nombre_apellidos']); ?></strong>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php echo !empty($actividad['convergente']) ? htmlspecialchars($actividad['convergente']) : '<span class="text-muted">-</span>'; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $etiquetas_cliente = $crud->getEtiquetasClienteCompletas($actividad['cliente_id']);
                                                    if ($etiquetas_cliente->num_rows > 0):
                                                        while ($etiq = $etiquetas_cliente->fetch_assoc()):
                                                    ?>
                                                        <span class="badge" style="background-color: <?php echo htmlspecialchars($etiq['color']); ?>;">
                                                            <?php echo htmlspecialchars($etiq['nombre']); ?>
                                                        </span>
                                                    <?php
                                                        endwhile;
                                                    else:
                                                    ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $actividad['tipo']; ?></td>
                                                <td>
                                                    <?php if (!empty($actividad['descripcion'])): ?>
                                                        <?php echo htmlspecialchars($actividad['descripcion']); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?php 
                                                        if ($actividad['completada']) {
                                                            echo 'secondary';
                                                        } elseif ($fecha_actividad < $hoy) {
                                                            echo 'danger';
                                                        } elseif ($fecha_actividad == $hoy) {
                                                            echo 'success';
                                                        } else {
                                                            echo 'info';
                                                        }
                                                    ?>">
                                                        <?php echo $fecha_formateada; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($actividad['completada'] == 1): ?>
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check-circle"></i> Completada
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-hourglass-half"></i> Pendiente
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="actividad_id" value="<?php echo $actividad['id']; ?>">
                                                        <input type="hidden" name="estado" value="<?php echo $actividad['completada'] == 1 ? 0 : 1; ?>">
                                                        <button type="submit" name="marcar_completada" class="btn btn-sm <?php echo $actividad['completada'] == 1 ? 'btn-warning' : 'btn-success'; ?>">
                                                            <i class="fas fa-<?php echo $actividad['completada'] == 1 ? 'undo' : 'check'; ?>"></i>
                                                            <?php echo $actividad['completada'] == 1 ? 'Desmarcar' : 'Completar'; ?>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- PAGINACIÓN -->
                                <?php if ($total_paginas > 1): ?>
                                <nav aria-label="Paginación" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($pagina > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="actividades.php?filtro=<?php echo $filtro; ?>&pagina=1<?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>"><i class="fas fa-chevron-left"></i> Primera</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="actividades.php?filtro=<?php echo $filtro; ?>&pagina=<?php echo $pagina - 1; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>">Anterior</a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                            <?php if ($i == $pagina): ?>
                                                <li class="page-item active">
                                                    <span class="page-link"><?php echo $i; ?></span>
                                                </li>
                                            <?php elseif ($i >= $pagina - 2 && $i <= $pagina + 2): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="actividades.php?filtro=<?php echo $filtro; ?>&pagina=<?php echo $i; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endfor; ?>

                                        <?php if ($pagina < $total_paginas): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="actividades.php?filtro=<?php echo $filtro; ?>&pagina=<?php echo $pagina + 1; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>">Siguiente</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="actividades.php?filtro=<?php echo $filtro; ?>&pagina=<?php echo $total_paginas; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>">Última <i class="fas fa-chevron-right"></i></a>
                                            </li>
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

    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js"></script>
    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
