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

// Verificar que sea admin (rol = 'encargado')
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

// Filtros
$tienda_filtro = $_GET['tienda'] ?? null;
$busqueda = $_GET['busqueda'] ?? '';
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$por_pagina = 30;

// Obtener todas las tiendas
$todas_tiendas = $crud->getTodasTiendas();

// Obtener todas las etiquetas
$todas_etiquetas = $crudAct->getTodasEtiquetasSistema();

// Calcular total de páginas
if ($tienda_filtro) {
    $tienda_filtro = intval($tienda_filtro);
    $total_paginas = $crud->getTotalPaginasTienda($tienda_filtro, $por_pagina);
    $total_clientes = $crud->getTotalClientesTienda($tienda_filtro);
} else {
    $total_paginas = $crud->getTotalPaginasSistema($por_pagina);
    $total_clientes = $crud->getTotalClientesSistema();
}

if ($pagina < 1) $pagina = 1;
if ($pagina > $total_paginas && $total_paginas > 0) $pagina = $total_paginas;

// Obtener clientes
if ($tienda_filtro) {
    $resultado = $crud->getClientesTiendaPaginados($tienda_filtro, $pagina, $por_pagina);
} else {
    $resultado = $crud->getClientesSistemaPaginados($pagina, $por_pagina);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PhoneCRM - Clientes (Admin)</title>
    <link href="../startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/favicon.png">

</head>
<body id="page-top">

    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("./includes/navbar.php"); ?>
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
                        <h1 class="h3 mb-0 text-gray-800">Clientes (Sistema Completo)</h1>
                    </div>

                    <!-- Filtros y búsqueda -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Búsqueda y Filtros</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="form-row">
                                <!-- Filtro por tienda -->
                                <div class="col-md-4 mb-3">
                                    <label for="tienda"><strong>Filtrar por Tienda</strong></label>
                                    <select name="tienda" class="form-control" id="tienda" onchange="this.form.submit();">
                                        <option value="">-- Todas las tiendas --</option>
                                        <?php 
                                        if ($todas_tiendas->num_rows > 0):
                                            while ($tienda = $todas_tiendas->fetch_assoc()):
                                                $is_selected = $tienda_filtro == $tienda['id'];
                                        ?>
                                            <option value="<?php echo $tienda['id']; ?>" <?php echo $is_selected ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($tienda['nombre']); ?>
                                            </option>
                                        <?php
                                            endwhile;
                                        endif;
                                        ?>
                                    </select>
                                </div>

                                <!-- Búsqueda por nombre -->
                                <div class="col-md-4 mb-3">
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

                                <!-- Limpiar -->
                                <div class="col-md-4 mb-3">
                                    <?php if ($tienda_filtro || !empty($busqueda)): ?>
                                        <label>&nbsp;</label><br>
                                        <a href="clientes.php" class="btn btn-secondary btn-block">
                                            <i class="fas fa-times"></i> Limpiar filtros
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabla de clientes -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Clientes 
                                <?php 
                                if ($tienda_filtro) {
                                    echo "- Tienda seleccionada";
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
                                    <p class="text-gray-500">No se encontraron clientes</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="18%">Nombre</th>
                                                <th width="10%">Teléfono</th>
                                                <th width="12%">Tienda</th>
                                                <th width="15%">Convergente</th>
                                                <th width="15%">Etiquetas</th>
                                                <th width="15%">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($cliente = $resultado->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($cliente['nombre_apellidos']); ?></strong><br>
                                                    <small class="text-muted">ID: <?php echo $cliente['id']; ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        <?php echo htmlspecialchars($cliente['tienda_nombre'] ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo !empty($cliente['convergente']) ? htmlspecialchars($cliente['convergente']) : '<span class="text-muted">-</span>'; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $etiquetas = $crud->getEtiquetas($cliente['id']);
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
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="ver_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Ver</a>
                                                    <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
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
                                                <a class="page-link" href="clientes.php?pagina=1<?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?>"><i class="fas fa-chevron-left"></i> Primera</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="clientes.php?pagina=<?php echo $pagina - 1; ?><?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?>">Anterior</a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                            <?php if ($i == $pagina): ?>
                                                <li class="page-item active">
                                                    <span class="page-link"><?php echo $i; ?></span>
                                                </li>
                                            <?php elseif ($i >= $pagina - 2 && $i <= $pagina + 2): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="clientes.php?pagina=<?php echo $i; ?><?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endfor; ?>

                                        <?php if ($pagina < $total_paginas): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="clientes.php?pagina=<?php echo $pagina + 1; ?><?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?>">Siguiente</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="clientes.php?pagina=<?php echo $total_paginas; ?><?php echo $tienda_filtro ? '&tienda=' . $tienda_filtro : ''; ?>">Última <i class="fas fa-chevron-right"></i></a>
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

    <script src="../startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js"></script>
    <script src="../startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
