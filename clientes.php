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

// Filtros y búsqueda
$busqueda = $_GET['busqueda'] ?? '';
$etiqueta_filtro = $_GET['etiqueta'] ?? null;
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$por_pagina = 30;

// Obtener todas las etiquetas del sistema
$todas_etiquetas = $crudAct->getTodasEtiquetasSistema();

// Calcular total de páginas
if (!empty($busqueda)) {
    $total_paginas = $crud->getTotalPaginasBusqueda($busqueda, $por_pagina);
} elseif ($etiqueta_filtro) {
    $etiqueta_filtro = intval($etiqueta_filtro);
    $total_paginas = $crud->getTotalPaginasClientesEtiqueta($etiqueta_filtro, $por_pagina);
} else {
    $total_paginas = $crud->getTotalPaginasClientes($por_pagina);
}

if ($pagina < 1) $pagina = 1;
if ($pagina > $total_paginas && $total_paginas > 0) $pagina = $total_paginas;

// Procesar formularios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['agregar_actividad'])) {
        $cliente_id = intval($_POST['cliente_id']);
        $tipo = $_POST['tipo'];
        $descripcion = trim($_POST['descripcion']);
        $fecha = $_POST['fecha'];
        
        if (!empty($cliente_id) && !empty($tipo) && !empty($fecha)) {
            $crudAct->agregarActividad($cliente_id, $tipo, $descripcion, $fecha);
            $crud->actualizarUltimaVisita($cliente_id);
        }
    }
    
    if (isset($_POST['marcar_completada'])) {
        $actividad_id = intval($_POST['actividad_id']);
        $estado = intval($_POST['estado']);
        $cliente_id = intval($_POST['cliente_id'] ?? 0);
        
        $crudAct->marcarCompletada($actividad_id, $estado);
        if ($cliente_id > 0) {
            $crud->actualizarUltimaVisita($cliente_id);
        }
    }
}

// Obtener clientes según filtros
if (!empty($busqueda)) {
    $total_clientes = 0; // No usar en búsqueda
    $resultado = $crud->buscarClientesPaginados($busqueda, $pagina, $por_pagina);
} elseif ($etiqueta_filtro) {
    $total_clientes = 0; // No usar en filtro
    $resultado = $crud->getClientesPaginadosPorEtiqueta($etiqueta_filtro, $pagina, $por_pagina);
} else {
    $total_clientes = $crud->getTotalClientes();
    $resultado = $crud->getClientesPaginados($pagina, $por_pagina);
}

// Sesión
$cliente_abierto = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 
                   (isset($_SESSION['cliente_abierto']) ? $_SESSION['cliente_abierto'] : null);
if ($cliente_abierto) {
    $_SESSION['cliente_abierto'] = $cliente_abierto;
} else {
    unset($_SESSION['cliente_abierto']);
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
    <link href="css/main.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">

</head>
<body id="page-top">

    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("./admin/includes/navbar.php"); ?>
                <div class="container-fluid">
                    <div class="mb-4 mt-4">
                        <a href="anadir_cliente.php" class="btn btn-primary">
                            <i class="fas fa-plus fa-fw"></i> Nuevo Cliente
                        </a>
                    </div>

                    <!-- Filtros y búsqueda -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Búsqueda y Filtros</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="form-row">
                                <!-- Búsqueda por nombre -->
                                <div class="col-md-6 mb-3">
                                    <label for="busqueda"><strong>Buscar por Nombre</strong></label>
                                    <div class="input-group">
                                        <input type="text" name="busqueda" class="form-control" id="busqueda" 
                                               placeholder="Nombre y apellidos..." value="<?php echo htmlspecialchars($busqueda); ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filtro por etiqueta -->
                                <div class="col-md-6 mb-3">
                                    <label for="etiqueta"><strong>Filtrar por Etiqueta</strong></label>
                                    <select name="etiqueta" class="form-control" id="etiqueta" onchange="this.form.submit();">
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
                                </div>
                            </form>

                            <!-- Botones de limpiar -->
                            <div class="mt-2">
                                <?php if (!empty($busqueda) || $etiqueta_filtro): ?>
                                    <a href="clientes.php" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-times"></i> Limpiar filtros
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Leyenda -->
                    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong><i class="fas fa-info-circle fa-fw"></i> Leyenda de colores:</strong>
                        <br>
                        <span class="badge badge-secondary">GRIS</span> Futuro &nbsp;
                        <span class="badge badge-success">VERDE</span> Hoy &nbsp;
                        <span class="badge badge-danger">ROJO</span> Pasado &nbsp;
                        <span class="badge badge-secondary">✓</span> Completada
                    </div>

                    <!-- Tabla de clientes -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Tabla de Clientes 
                                <?php 
                                if (!empty($busqueda)) {
                                    echo "- Búsqueda: " . htmlspecialchars($busqueda);
                                } elseif ($etiqueta_filtro) {
                                    echo "- Filtrado por etiqueta";
                                } else {
                                    echo "(" . $total_clientes . " total)";
                                }
                                ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php 
                            $total_mostrados = $resultado->num_rows;
                            if ($total_mostrados == 0): 
                            ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-gray-500 mb-3">
                                        <?php 
                                        if (!empty($busqueda)) {
                                            echo "No se encontraron clientes con ese nombre";
                                        } elseif ($etiqueta_filtro) {
                                            echo "No hay clientes con esa etiqueta";
                                        } else {
                                            echo "No hay clientes registrados aún";
                                        }
                                        ?>
                                    </p>
                                    <a href="anadir_cliente.php" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Añadir Cliente
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
                                            <?php while ($cliente = $resultado->fetch_assoc()): 
                                                $es_abierto = $cliente_abierto && $cliente['id'] == $cliente_abierto;
                                            ?>
                                                <tr class="cliente-principal" data-cliente-id="<?php echo $cliente['id']; ?>" style="cursor: pointer;">
                                                    <td onclick="toggleExpand(event, <?php echo $cliente['id']; ?>)">
                                                        <strong><?php echo htmlspecialchars($cliente['nombre_apellidos']); ?></strong><br>
                                                        <small class="text-muted">ID: <?php echo $cliente['id']; ?> | DNI: <?php echo htmlspecialchars($cliente['dni']); ?></small>
                                                    </td>
                                                    <td onclick="toggleExpand(event, <?php echo $cliente['id']; ?>)">
                                                        <?php echo htmlspecialchars($cliente['telefono']); ?>
                                                    </td>
                                                    <td onclick="toggleExpand(event, <?php echo $cliente['id']; ?>)">
                                                        <?php
                                                        $actividad = $crudAct->getProximaActividad($cliente['id']);
                                                        if ($actividad):
                                                            $fecha_format = date('d/m/Y', strtotime($actividad['fecha']));
                                                            $tipo = $actividad['tipo'];
                                                            $color_fecha = crudClientes::getColorActividad($actividad['fecha'], 0);
                                                        ?>
                                                            <span class="badge proxima-<?php echo $color_fecha; ?>"><?php echo $tipo; ?></span>
                                                            <small class="d-block"><?php echo $fecha_format; ?></small>
                                                        <?php else: ?>
                                                            <small class="text-muted">Sin actividades pendientes</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td onclick="toggleExpand(event, <?php echo $cliente['id']; ?>)">
                                                        <?php
                                                        $etiquetas = $crud->getEtiquetas($cliente['id']);
                                                        if ($etiquetas->num_rows > 0):
                                                            while ($etiqueta = $etiquetas->fetch_assoc()):
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
                                                        <a href="ver_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Ver</a>
                                                        <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
                                                        <a href="eliminar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')"><i class="fas fa-trash"></i> Eliminar</a>
                                                    </td>
                                                </tr>
                                                <tr class="expandible-row row-actividades" id="expand-<?php echo $cliente['id']; ?>" style="display: <?php echo $es_abierto ? 'table-row' : 'none'; ?>;">
                                                    <td colspan="5">
                                                        <div class="p-4">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6><i class="fas fa-list"></i> Actividades</h6>
                                                                    <?php
                                                                    $actividades = $crudAct->getActividades($cliente['id']);
                                                                    if ($actividades->num_rows > 0):
                                                                        while ($act = $actividades->fetch_assoc()):
                                                                            $color = crudClientes::getColorActividad($act['fecha'], $act['completada']);
                                                                    ?>
                                                                        <div class="actividad-item <?php echo 'actividad-' . $color; ?>">
                                                                            <div class="actividad-contenido">
                                                                                <strong><?php echo $act['tipo']; ?></strong>
                                                                                <small class="ml-2"><?php echo date('d/m/Y', strtotime($act['fecha'])); ?></small>
                                                                                <?php if (!empty($act['descripcion'])): ?>
                                                                                <p class="mb-0 mt-2"><?php echo htmlspecialchars($act['descripcion']); ?></p>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                            <form method="POST" class="actividad-checkbox" onclick="event.stopPropagation();">
                                                                                <input type="hidden" name="actividad_id" value="<?php echo $act['id']; ?>">
                                                                                <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                                                                                <input type="hidden" name="estado" value="<?php echo $act['completada'] == 1 ? 0 : 1; ?>">
                                                                                <button type="submit" name="marcar_completada" class="btn btn-sm <?php echo $act['completada'] == 1 ? 'btn-success' : 'btn-outline-success'; ?>">
                                                                                    <i class="fas fa-check"></i> <?php echo $act['completada'] == 1 ? 'Desmarcar' : 'Completar'; ?>
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    <?php
                                                                        endwhile;
                                                                    else:
                                                                    ?>
                                                                    <p class="text-muted">Sin actividades</p>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h6><i class="fas fa-calendar-plus"></i> Nueva Actividad</h6>
                                                                    <form method="POST" style="font-size: 0.95rem;" onclick="event.stopPropagation();">
                                                                        <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                                                                        <div class="form-group mb-2">
                                                                            <select name="tipo" class="form-control form-control-sm" required>
                                                                                <option value="">-- Tipo --</option>
                                                                                <option value="Llamada">📞 Llamada</option>
                                                                                <option value="WhatsApp">💬 WhatsApp</option>
                                                                                <option value="Cita">📅 Cita</option>
                                                                                <option value="Revisión">🔍 Revisión</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group mb-2">
                                                                            <input type="date" name="fecha" class="form-control form-control-sm" required>
                                                                        </div>
                                                                        <div class="form-group mb-2">
                                                                            <textarea name="descripcion" class="form-control form-control-sm" rows="2" placeholder="Descripción..."></textarea>
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

                                <!-- PAGINACIÓN -->
                                <?php if ($total_paginas > 1): ?>
                                <nav aria-label="Paginación" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($pagina > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="clientes.php?pagina=1<?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>"><i class="fas fa-chevron-left"></i> Primera</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="clientes.php?pagina=<?php echo $pagina - 1; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>">Anterior</a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                            <?php if ($i == $pagina): ?>
                                                <li class="page-item active">
                                                    <span class="page-link"><?php echo $i; ?></span>
                                                </li>
                                            <?php elseif ($i >= $pagina - 2 && $i <= $pagina + 2): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="clientes.php?pagina=<?php echo $i; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endfor; ?>

                                        <?php if ($pagina < $total_paginas): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="clientes.php?pagina=<?php echo $pagina + 1; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>">Siguiente</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="clientes.php?pagina=<?php echo $total_paginas; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>">Última <i class="fas fa-chevron-right"></i></a>
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

    <script>
        function toggleExpand(event, clienteId) {
            if (event.target.closest('.btn') || event.target.closest('form')) {
                return;
            }
            
            const expandRow = document.getElementById('expand-' + clienteId);
            
            document.querySelectorAll('.expandible-row').forEach(function(row) {
                if (row.id !== 'expand-' + clienteId) {
                    row.style.display = 'none';
                }
            });
            
            if (expandRow.style.display === 'none') {
                expandRow.style.display = 'table-row';
            } else {
                expandRow.style.display = 'none';
            }
        }
    </script>

</body>
</html>
