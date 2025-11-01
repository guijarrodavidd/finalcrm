<?php
session_start();
include("./includes/database.php");
include("./includes/crudAdmin.php");

$connClass = new Connection();
$conexion = $connClass->getConnection();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Verificar que es encargado
if ($_SESSION['usuario_rol'] != 'encargado') {
    header("Location: ../principal.php");
    exit();
}

$crudAdmin = new CrudAdmin($conexion);

// Obtener datos del dashboard (DE TODAS LAS TIENDAS)
$total_clientes = $crudAdmin->getTotalClientesSistema();
$actividades_pendientes = $crudAdmin->getActividadesPendientesSistema();
$actividades_hoy = $crudAdmin->getActividadesHoySistema();
$clientes_recientes = $crudAdmin->getClientesRecientesSistema();
$proximas_actividades = $crudAdmin->getProximasActividadesSistema();
$hoy = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PhoneCRM - Admin Dashboard</title>
    <link href="../startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">

    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("./includes/navbar.php"); ?>
                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin - Sistema General</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Total Clientes Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-primary text-uppercase mb-1">Total Clientes (Sistema)</div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo $total_clientes; ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Actividades Pendientes Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-warning text-uppercase mb-1">Actividades Pendientes (Sistema)</div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo $actividades_pendientes; ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Actividades Hoy Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-success text-uppercase mb-1">Actividades Hoy (Sistema)</div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo $actividades_hoy; ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Visitas Recientes Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-info text-uppercase mb-1">Visitas Recientes (Sistema)</div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo $clientes_recientes->num_rows; ?></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Clientes Recientes -->
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Clientes Visitados Recientemente</h6>
                                </div>
                                <div class="card-body">
                                    <?php if ($clientes_recientes->num_rows > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Teléfono</th>
                                                        <th>Última Visita</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($cliente = $clientes_recientes->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($cliente['nombre_apellidos']); ?></td>
                                                            <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                                                            <td><?php echo date('d/m/Y H:i', strtotime($cliente['ultima_visita'])); ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">No hay clientes visitados recientemente</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Próximas Actividades -->
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Próximas Actividades (7 días)</h6>
                                </div>
                                <div class="card-body">
                                    <?php if ($proximas_actividades->num_rows > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Cliente</th>
                                                        <th>Tipo</th>
                                                        <th>Fecha</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($actividad = $proximas_actividades->fetch_assoc()): 
                                                        $fecha_formateada = date('d/m/Y', strtotime($actividad['fecha']));
                                                    ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($actividad['nombre_apellidos']); ?></td>
                                                            <td><?php echo $actividad['tipo']; ?></td>
                                                            <td><?php echo $fecha_formateada; ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">No hay actividades próximas</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; PhoneCRM 2025 - Área de Administrador</span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <script src="../startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js"></script>
    <script src="../startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
