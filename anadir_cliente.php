<?php
session_start();
include("./admin/includes/database.php");

$connClass = new Connection();
$conexion = $connClass->getConnection();

// Verificar sesión antes de mostrar el formulario
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$exito = '';

// Validación sencilla de DNI
function validarDNI($dni) {
    $dni = str_replace(" ", "", $dni);
    $dni = strtoupper($dni);
    if (strlen($dni) != 9) return false;
    $numeros = substr($dni, 0, 8);
    if (!is_numeric($numeros)) return false;
    $letra = substr($dni, 8, 1);
    if (!ctype_alpha($letra)) return false;
    return true;
}

// Validación sencilla de teléfono
function validarTelefono($telefono) {
    $telefono = str_replace(" ", "", $telefono);
    $telefono = str_replace("-", "", $telefono);
    if (!is_numeric($telefono)) return false;
    $longitud = strlen($telefono);
    if ($longitud < 7 || $longitud > 9) return false;
    return true;
}

// Guardar cliente en la base de datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_apellidos = isset($_POST['nombre_apellidos']) ? trim($_POST['nombre_apellidos']) : '';
    $dni = isset($_POST['dni']) ? trim($_POST['dni']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $etiqueta_id = isset($_POST['etiqueta_id']) ? $_POST['etiqueta_id'] : 0;
    $notas = isset($_POST['notas']) ? trim($_POST['notas']) : '';
    $convergente = isset($_POST['convergente']) ? trim($_POST['convergente']) : '';

    if (empty($nombre_apellidos)) {
        $error = "El nombre y apellidos son obligatorios.";
    } elseif (empty($dni)) {
        $error = "El DNI es obligatorio.";
    } elseif (!validarDNI($dni)) {
        $error = "El DNI no es válido. Debe tener 8 números seguidos de una letra, ejemplo: 12345678A.";
    } elseif (empty($telefono)) {
        $error = "El teléfono es obligatorio.";
    } elseif (!validarTelefono($telefono)) {
        $error = "El teléfono debe tener entre 7 y 9 números y sin símbolos.";
    } elseif ($etiqueta_id == 0) {
        $error = "Debes seleccionar una etiqueta.";
    } else {
        $telefono_limpio = str_replace([" ", "-"], "", $telefono);
        $dni = strtoupper($dni);
        $usuario_id = $_SESSION['usuario_id'];
        $query = "INSERT INTO clientes (nombre_apellidos, dni, telefono, notas, convergente, etiqueta_id, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssii", $nombre_apellidos, $dni, $telefono_limpio, $notas, $convergente, $etiqueta_id, $usuario_id);

            if (mysqli_stmt_execute($stmt)) {
                $exito = "Cliente añadido correctamente.";
                $nombre_apellidos = '';
                $dni = '';
                $telefono = '';
                $etiqueta_id = 0;
                $notas = '';
                $convergente = '';
            } else {
                $error = "Error al guardar el cliente. Detalle técnico: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Error en la consulta. Detalle: " . mysqli_error($conexion);
        }
    }
}

$query_etiquetas = "SELECT id, nombre FROM etiquetas ORDER BY nombre";
$resultado_etiquetas = mysqli_query($conexion, $query_etiquetas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PhoneCRM - Añadir Cliente</title>
    <link href="startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .form-control:focus { border-color: #004085; box-shadow: 0 0 0 0.2rem rgba(0, 64, 133, 0.25); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.10);}
        .card-header { background-color: #0074d9; color: white; }
        .label-required::after { content: " *"; color: red; }
        .custom-header {
            background: linear-gradient(90deg, #0074d9 70%, #00c6fb 100%);
            color: white; text-align: center; padding: 32px 0; margin-bottom: 32px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
        }
        .custom-header h1 { font-size: 2.5rem; font-weight: bold; margin-bottom: 8px; }
        .custom-header p { font-size: 1.1rem; }
        .card.shadow { border-radius: 14px; }
    </style>
</head>
<body>
    <div class="custom-header">
        <h1><i class="fas fa-user-plus"></i> Añadir Cliente</h1>
        <p>Completa el formulario para registrar un nuevo cliente en PhoneCRM</p>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                <?php endif; ?>
                <?php if (!empty($exito)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $exito; ?>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                <?php endif; ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold">Información del Cliente</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="anadir_cliente.php">
                            <div class="form-group">
                                <label for="nombre_apellidos" class="label-required">Nombre y Apellidos</label>
                                <input type="text" class="form-control" id="nombre_apellidos" name="nombre_apellidos"
                                       placeholder="Juan García Pérez" value="<?php echo isset($nombre_apellidos) ? $nombre_apellidos : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="dni" class="label-required">DNI</label>
                                <input type="text" class="form-control" id="dni" name="dni"
                                       placeholder="12345678A" value="<?php echo isset($dni) ? $dni : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono" class="label-required">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono"
                                       placeholder="634567890" value="<?php echo isset($telefono) ? $telefono : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="etiqueta_id" class="label-required">Etiqueta</label>
                                <select class="form-control" id="etiqueta_id" name="etiqueta_id" required>
                                    <option value="0">-- Selecciona una etiqueta --</option>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($resultado_etiquetas)) {
                                        $selected = ($etiqueta_id == $row['id']) ? 'selected' : '';
                                        echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['nombre'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="convergente">Paquete Convergente (Fibra + Móvil)</label>
                                <input type="text" class="form-control" id="convergente" name="convergente"
                                       placeholder="Fibra 600Mb + 2 líneas 50GB" value="<?php echo isset($convergente) ? $convergente : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="notas">Notas</label>
                                <textarea class="form-control" id="notas" name="notas" rows="3" placeholder="Observaciones..."><?php echo isset($notas) ? $notas : ''; ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Guardar Cliente</button>
                            <a href="principal.php" class="btn btn-secondary btn-block mt-2"><i class="fas fa-arrow-left"></i> Volver</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js"></script>
    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="startbootstrap-sb-admin-2-gh-pages/js/sb-admin-2.min.js"></script>
</body>
</html>
