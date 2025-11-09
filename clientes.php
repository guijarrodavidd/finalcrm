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

$busqueda = $_GET['busqueda'] ?? '';
$etiqueta_filtro = $_GET['etiqueta'] ?? null;
$pagina = max(1, isset($_GET['pagina']) ? intval($_GET['pagina']) : 1);
$por_pagina = 30;

$todas_etiquetas = $crudAct->getTodasEtiquetasSistema();

$total_clientes = 0;
$total_paginas = 0;
$resultado = null;

if (!empty($busqueda)) {
    $total_clientes = 0;
    $resultado = $crud->buscarClientesPaginados($busqueda, $pagina, $por_pagina);
    $total_paginas = $crud->getTotalPaginasBusqueda($busqueda, $por_pagina);
} elseif ($etiqueta_filtro) {
    $etiqueta_filtro = intval($etiqueta_filtro);
    $resultado = $crud->getClientesPaginadosPorEtiqueta($etiqueta_filtro, $pagina, $por_pagina);
    $total_paginas = $crud->getTotalPaginasClientesEtiqueta($etiqueta_filtro, $por_pagina);
    $total_clientes = 0;
} else {
    $total_clientes = $crud->getTotalClientes();
    $total_paginas = ceil($total_clientes / $por_pagina);
    $resultado = $crud->getClientesPaginados($pagina, $por_pagina);
}

if ($pagina > $total_paginas && $total_paginas > 0) {
    $pagina = $total_paginas;
    if (!empty($busqueda)) {
        $resultado = $crud->buscarClientesPaginados($busqueda, $pagina, $por_pagina);
    } elseif ($etiqueta_filtro) {
        $resultado = $crud->getClientesPaginadosPorEtiqueta($etiqueta_filtro, $pagina, $por_pagina);
    } else {
        $resultado = $crud->getClientesPaginados($pagina, $por_pagina);
    }
}

$total_mostrados = 0;
$clientes_array = array();

if ($resultado) {
    while ($cliente = $resultado->fetch_assoc()) {
        $clientes_array[] = $cliente;
        $total_mostrados++;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['eliminar_cliente'])) {
        $cliente_id = intval($_POST['eliminar_cliente']);
        
        $sql_notas = "DELETE FROM notas WHERE cliente_id = ?";
        $stmt_notas = $conexion->prepare($sql_notas);
        $stmt_notas->bind_param("i", $cliente_id);
        $stmt_notas->execute();
        
        $sql_act = "DELETE FROM actividades WHERE cliente_id = ?";
        $stmt_act = $conexion->prepare($sql_act);
        $stmt_act->bind_param("i", $cliente_id);
        $stmt_act->execute();
        
        $sql = "DELETE FROM clientes WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
    }

    if (isset($_POST['actualizar_visita'])) {
        $cliente_id = intval($_POST['cliente_id']);
        $crud->actualizarUltimaVisita($cliente_id);
    }
    
    if (isset($_POST['agregar_actividad'])) {
        $cliente_id = intval($_POST['cliente_id']);
        $tipo = $_POST['tipo'] ?? '';
        $descripcion = trim($_POST['descripcion'] ?? '');
        $fecha = $_POST['fecha'] ?? '';
        
        if ($cliente_id && $tipo && $fecha) {
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

    if (isset($_POST['agregar_nota'])) {
        $cliente_id = intval($_POST['cliente_id']);
        $texto = trim($_POST['nueva_nota'] ?? '');
        
        if ($cliente_id && !empty($texto)) {
            $sql = "INSERT INTO notas (cliente_id, texto, fecha_creacion) VALUES (?, ?, NOW())";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("is", $cliente_id, $texto);
            $stmt->execute();
            $crud->actualizarUltimaVisita($cliente_id);
        }
    }

    if (isset($_POST['eliminar_nota'])) {
        $nota_id = intval($_POST['eliminar_nota']);
        $sql = "DELETE FROM notas WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $nota_id);
        $stmt->execute();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("./admin/includes/navbar_usuario.php"); ?>
                <div class="container-fluid">
                    
                    <div class="mb-4 mt-4">
                        <a href="anadir_cliente.php" class="btn btn-primary">➕ Nuevo Cliente</a>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">🔍 Búsqueda y Filtros</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label><strong>Buscar por Nombre</strong></label>
                                    <div class="input-group">
                                        <input type="text" name="busqueda" class="form-control" placeholder="Nombre y apellidos..." value="<?php echo htmlspecialchars($busqueda); ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">🔎 Buscar</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label><strong>Filtrar por Etiqueta</strong></label>
                                    <select name="etiqueta" class="form-control" onchange="this.form.submit();">
                                        <option value="">-- Todas las etiquetas --</option>
                                        <?php 
                                        if ($todas_etiquetas && $todas_etiquetas->num_rows > 0):
                                            while ($et = $todas_etiquetas->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $et['id']; ?>" <?php echo ($etiqueta_filtro == $et['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($et['nombre']); ?>
                                            </option>
                                        <?php endwhile; endif; ?>
                                    </select>
                                </div>
                            </form>
                            <?php if (!empty($busqueda) || $etiqueta_filtro): ?>
                                <a href="clientes.php" class="btn btn-secondary btn-sm">✖️ Limpiar Filtros</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">👥 Clientes (<?php echo $total_mostrados; ?> mostrados)</h6>
                        </div>
                        <div class="card-body">
                            <?php if ($total_mostrados == 0): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted">No hay clientes para mostrar</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Teléfono</th>
                                                <th>DNI</th>
                                                <th>Próxima Actividad</th>
                                                <th>Notas</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($clientes_array as $cliente): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($cliente['nombre_apellidos'] ?? ''); ?></strong>
                                                    <br>
                                                    <small class="text-muted">ID: <?php echo $cliente['id']; ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($cliente['telefono'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($cliente['dni'] ?? '-'); ?></td>
                                                <td>
                                                    <?php
                                                    $act = $crudAct->getProximaActividad($cliente['id']);
                                                    if ($act):
                                                        $color = crudClientes::getColorActividad($act['fecha'], 0);
                                                    ?>
                                                        <span class="badge badge-<?php echo $color; ?>"><?php echo $act['tipo']; ?></span>
                                                        <small><?php echo date('d/m/Y', strtotime($act['fecha'])); ?></small>
                                                    <?php else: ?>
                                                        <small class="text-muted">-</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $sql_notas = "SELECT id, texto FROM notas WHERE cliente_id = ? ORDER BY fecha_creacion DESC LIMIT 1";
                                                    $stmt_notas = $conexion->prepare($sql_notas);
                                                    $stmt_notas->bind_param("i", $cliente['id']);
                                                    $stmt_notas->execute();
                                                    $resultado_notas = $stmt_notas->get_result();
                                                    
                                                    if ($resultado_notas->num_rows > 0):
                                                        $ultima_nota = $resultado_notas->fetch_assoc();
                                                    ?>
                                                        <small title="<?php echo htmlspecialchars($ultima_nota['texto']); ?>"><?php echo substr(htmlspecialchars($ultima_nota['texto']), 0, 40); ?>...</small>
                                                    <?php else: ?>
                                                        <small class="text-muted">-</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-success btn-sm" type="button" data-toggle="collapse" data-target="#form-actividad-<?php echo $cliente['id']; ?>">
                                                        ➕
                                                    </button>
                                                    <a href="ver_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-info btn-sm">👁️</a>
                                                    <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-warning btn-sm">✏️</a>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="eliminar_cliente" value="<?php echo $cliente['id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este cliente? Esta acción no se puede deshacer.')">🗑️ Eliminar</button>
                                                    </form>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="6">
                                                    <div class="collapse" id="form-actividad-<?php echo $cliente['id']; ?>">
                                                        <div class="card card-body mt-2 mb-2">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6>📋 Actividades</h6>
                                                                    <?php
                                                                    $actividades = $crudAct->getActividades($cliente['id']);
                                                                    if ($actividades && $actividades->num_rows > 0):
                                                                        while ($act = $actividades->fetch_assoc()):
                                                                            $color = crudClientes::getColorActividad($act['fecha'], $act['completada']);
                                                                    ?>
                                                                        <div class="alert alert-<?php echo $color; ?> p-2 mb-2">
                                                                            <strong><?php echo $act['tipo']; ?></strong>
                                                                            <small class="d-block"><?php echo date('d/m/Y', strtotime($act['fecha'])); ?></small>
                                                                            <?php if (!empty($act['descripcion'])): ?>
                                                                                <small class="d-block text-muted"><?php echo htmlspecialchars($act['descripcion']); ?></small>
                                                                            <?php endif; ?>
                                                                            <form method="POST" class="mt-1" style="display:inline;">
                                                                                <input type="hidden" name="actividad_id" value="<?php echo $act['id']; ?>">
                                                                                <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                                                                                <input type="hidden" name="estado" value="<?php echo $act['completada'] == 1 ? 0 : 1; ?>">
                                                                                <button type="submit" name="marcar_completada" class="btn btn-sm <?php echo $act['completada'] == 1 ? 'btn-success' : 'btn-outline-success'; ?>">
                                                                                    <?php echo $act['completada'] == 1 ? '✓ Desmarcar' : '✓ Completar'; ?>
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
                                                                    <h6>➕ Nueva Actividad</h6>
                                                                    <form method="POST">
                                                                        <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                                                                        
                                                                        <div class="form-group mb-2">
                                                                            <select name="tipo" class="form-control form-control-sm" required>
                                                                                <option value="">Tipo</option>
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
                                                                        
                                                                        <button type="submit" name="agregar_actividad" class="btn btn-primary btn-sm btn-block">💾 Agendar</button>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                            <hr>

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <h6>📝 Notas</h6>
                                                                    <?php
                                                                    $sql_todas_notas = "SELECT id, texto, fecha_creacion FROM notas WHERE cliente_id = ? ORDER BY fecha_creacion DESC";
                                                                    $stmt_todas_notas = $conexion->prepare($sql_todas_notas);
                                                                    $stmt_todas_notas->bind_param("i", $cliente['id']);
                                                                    $stmt_todas_notas->execute();
                                                                    $resultado_todas_notas = $stmt_todas_notas->get_result();
                                                                    
                                                                    if ($resultado_todas_notas->num_rows > 0):
                                                                        while ($nota = $resultado_todas_notas->fetch_assoc()):
                                                                    ?>
                                                                        <div class="alert alert-info p-2 mb-2">
                                                                            <small><?php echo htmlspecialchars($nota['texto']); ?></small><br>
                                                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($nota['fecha_creacion'])); ?></small>
                                                                            <form method="POST" style="display:inline; margin-left:10px;">
                                                                                <input type="hidden" name="eliminar_nota" value="<?php echo $nota['id']; ?>">
                                                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar nota?')">🗑️</button>
                                                                            </form>
                                                                        </div>
                                                                    <?php
                                                                        endwhile;
                                                                    else:
                                                                    ?>
                                                                        <p class="text-muted">Sin notas</p>
                                                                    <?php endif; ?>
                                                                    
                                                                    <h6 class="mt-3">➕ Nueva Nota</h6>
                                                                    <form method="POST">
                                                                        <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="text" name="nueva_nota" class="form-control" placeholder="Añade una nota..." required>
                                                                            <div class="input-group-append">
                                                                                <button type="submit" name="agregar_nota" class="btn btn-primary">➕</button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <?php if ($total_paginas > 1): ?>
                                <nav class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($pagina > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="clientes.php?pagina=1<?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>">Primera</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="clientes.php?pagina=<?php echo $pagina - 1; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>">Anterior</a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++): ?>
                                            <?php if ($i == $pagina): ?>
                                                <li class="page-item active"><span class="page-link"><?php echo $i; ?></span></li>
                                            <?php else: ?>
                                                <li class="page-item"><a class="page-link" href="clientes.php?pagina=<?php echo $i; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>"><?php echo $i; ?></a></li>
                                            <?php endif; ?>
                                        <?php endfor; ?>

                                        <?php if ($pagina < $total_paginas): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="clientes.php?pagina=<?php echo $pagina + 1; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>">Siguiente</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="clientes.php?pagina=<?php echo $total_paginas; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?><?php echo $etiqueta_filtro ? '&etiqueta=' . $etiqueta_filtro : ''; ?>">Última</a>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
