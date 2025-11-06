<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("./admin/includes/database.php");

$connClass = new Connection();
$conexion = $connClass->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = isset($_POST['usuario_id']) ? trim($_POST['usuario_id']) : '';
    $contraseña = isset($_POST['contraseña']) ? trim($_POST['contraseña']) : '';

    if (empty($usuario_id) || empty($contraseña)) {
        $error = "Por favor rellena todos los campos";
    } else {
        $query = "SELECT id, nombre, contraseña, rol FROM usuarios WHERE id = ?";
        $stmt = mysqli_prepare($conexion, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $usuario_id);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $usuario = mysqli_fetch_assoc($resultado);
            mysqli_stmt_close($stmt);

            if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];
                
                // Redirigir según el rol
                if ($usuario['rol'] == 'encargado') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: principal.php");
                }
                exit();
            } else {
                $error = "Usuario o contraseña incorrectos";
            }
        } else {
            $error = "Error en la consulta: " . mysqli_error($conexion);
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
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PhoneCRM - Login</title>

    <!-- Custom fonts for this template-->
    <link href="startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                <img src="./images/phonecrm.png" style="max-width: 500px;">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center mb-4">
                                        <img src="images/phonehouse.png" alt="PhoneCRM Logo" style="max-width: 300px;">
                                    </div>
                                    <h1 class="h4 text-gray-900 mb-4">¡Bienvenido a PhoneCRM!</h1>

                                    <!-- Mostrar error si existe -->
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?php echo htmlspecialchars($error); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <form class="user" method="POST" action="index.php">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user"
                                                id="usuario_id" name="usuario_id"
                                                placeholder="ID de Usuario" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="contraseña" name="contraseña" placeholder="Contraseña" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Recordarme</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Iniciar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js"></script>
    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="startbootstrap-sb-admin-2-gh-pages/vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="startbootstrap-sb-admin-2-gh-pages/js/sb-admin-2.min.js"></script>

</body>
</html>
