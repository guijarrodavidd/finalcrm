<?php
session_start();
include("./admin/includes/database.php");
require_once "./admin/includes/crudClientes.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$connClass = new Connection();
$conexion = $connClass->getConnection();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$crud = new crudClientes($conexion, $usuario_id);

$id = $_GET['id'] ?? null;
$cliente = null;
$mensaje = "";
$mensaje_error = "";

if (!$id) {
    header("Location: clientes.php");
    exit();
}

// Obtener cliente
$cliente = $crud->getClienteById($id);

if (!$cliente) {
    header("Location: clientes.php");
    exit();
}

// Obtener etiquetas actuales del cliente
$etiquetas_cliente_resultado = $crud->getEtiquetasClienteIds($id);
$etiquetas_cliente = [];
while ($row = $etiquetas_cliente_resultado->fetch_assoc()) {
    $etiquetas_cliente[] = $row['etiqueta_id'];
}

// Obtener todas las etiquetas disponibles
$todas_etiquetas = $crud->getTodasLasEtiquetas();

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_apellidos = trim($_POST['nombre_apellidos'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $convergente = trim($_POST['convergente'] ?? '');
    $etiquetas_seleccionadas = $_POST['etiquetas'] ?? [];

    if (empty($nombre_apellidos) || empty($telefono)) {
        $mensaje_error = "El nombre y teléfono son obligatorios.";
    } else {
        if ($crud->actualizarCliente($id, $nombre_apellidos, $dni, $telefono, $convergente)) {
            // Actualizar etiquetas
            $crud->eliminarEtiquetasCliente($id);
            
            foreach ($etiquetas_seleccionadas as $etiqueta_id) {
                $etiqueta_id = intval($etiqueta_id);
                $crud->agregarEtiquetaCliente($id, $etiqueta_id);
            }
            
            $mensaje = "Cliente actualizado correctamente.";
            $cliente = $crud->getClienteById($id);
            
            // Actualizar etiquetas mostradas
            $etiquetas_cliente_resultado = $crud->getEtiquetasClienteIds($id);
            $etiquetas_cliente = [];
            while ($row = $etiquetas_cliente_resultado->fetch_assoc()) {
                $etiquetas_cliente[] = $row['etiqueta_id'];
            }
        } else {
            $mensaje_error = "Error al actualizar el cliente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PhoneCRM - Editar Cliente</title>
    <link href="startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">

</head>
<body id="page-top">

    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("./admin/includes/navbar.php"); ?>
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
                        <h1 class="h3 mb-0 text-gray-800">Editar Cliente</h1>
                    </div>

                    <?php if ($mensaje): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($mensaje); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if ($mensaje_error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($mensaje_error); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Formulario de Edición</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label for="nombre_apellidos"><strong>Nombre y Apellidos</strong></label>
                                    <input type="text" class="form-control" id="nombre_apellidos" name="nombre_apellidos" value="<?php echo htmlspecialchars($cliente['nombre_apellidos']); ?>" required>
                                    <small class="form-text text-muted">Campo obligatorio</small>
                                </div>

                                <div class="form-group">
                                    <label for="dni"><strong>DNI</strong></label>
                                    <input type="text" class="form-control" id="dni" name="dni" value="<?php echo htmlspecialchars($cliente['dni']); ?>" placeholder="Ej: 12345678A">
                                </div>

                                <div class="form-group">
                                    <label for="telefono"><strong>Teléfono</strong></label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" required>
                                    <small class="form-text text-muted">Campo obligatorio</small>
                                </div>

                                <div class="form-group">
                                    <label for="convergente"><strong>Convergente</strong></label>
                                    <input type="text" class="form-control" id="convergente" name="convergente" value="<?php echo htmlspecialchars($cliente['convergente']); ?>" placeholder="Ej: Movistar, Vodafone...">
                                </div>

                                <hr>

                                <div class="form-group">
                                    <label><strong>Etiquetas</strong></label>
                                    <div class="card">
                                        <div class="card-body">
                                            <?php 
                                            $todas_etiquetas->data_seek(0);
                                            $tiene_etiquetas = false;
                                            while ($etiqueta = $todas_etiquetas->fetch_assoc()):
                                                $tiene_etiquetas = true;
                                                $checked = in_array($etiqueta['id'], $etiquetas_cliente) ? 'checked' : '';
                                            ?>
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input type="checkbox" class="custom-control-input" id="etiqueta_<?php echo $etiqueta['id']; ?>" name="etiquetas[]" value="<?php echo $etiqueta['id']; ?>" <?php echo $checked; ?>>
                                                    <label class="custom-control-label" for="etiqueta_<?php echo $etiqueta['id']; ?>">
                                                        <span style="display: inline-block; width: 12px; height: 12px; background-color: <?php echo htmlspecialchars($etiqueta['color']); ?>; border-radius: 3px; margin-right: 8px;"></span>
                                                        <?php echo htmlspecialchars($etiqueta['nombre']); ?>
                                                    </label>
                                                </div>
                                            <?php endwhile; ?>
                                            <?php if (!$tiene_etiquetas): ?>
                                                <p class="text-muted">No hay etiquetas disponibles</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group">
                                    <label><strong>ID Cliente</strong></label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($cliente['id']); ?>" disabled>
                                    <small class="form-text text-muted">No se puede modificar</small>
                                </div>

                                <div class="form-group">
                                    <label><strong>Fecha de Creación</strong></label>
                                    <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($cliente['fecha_creacion'])); ?>" disabled>
                                    <small class="form-text text-muted">No se puede modificar</small>
                                </div>

                                <div class="form-group">
                                    <label><strong>Última Visita</strong></label>
                                    <input type="text" class="form-control" value="<?php echo $cliente['ultima_visita'] ? date('d/m/Y H:i', strtotime($cliente['ultima_visita'])) : 'Sin visitas'; ?>" disabled>
                                    <small class="form-text text-muted">Se actualiza automáticamente</small>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-save"></i> Guardar Cambios
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="clientes.php" class="btn btn-secondary btn-block">
                                            <i class="fas fa-times"></i> Cancelar
                                        </a>
                                    </div>
                                </div>
                            </form>
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
