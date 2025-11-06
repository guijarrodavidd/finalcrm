<?php
session_start();
require_once "./includes/crudUsuarios.php";

// Verificar que es encargado
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 'encargado') {
    header("Location: ../index.php");
    exit();
}

$usuariosObj = new usuarios();
$listaUsuarios = $usuariosObj->showUsuarios();

$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;
$mensaje = "";
$mensaje_error = "";
$mostrar_formulario = false;

// Eliminar usuario
if ($accion === "eliminar" && $id) {
    if ($id == $_SESSION['usuario_id']) {
        $mensaje_error = "No puedes eliminar tu propio usuario.";
    } else {
        if ($usuariosObj->eliminarUsuario($id)) {
            $mensaje = "Usuario eliminado correctamente.";
            $listaUsuarios = $usuariosObj->showUsuarios();
        } else {
            $mensaje_error = "Error al eliminar el usuario.";
        }
    }
}

// Preparar datos del formulario
$usuario = ['id' => '', 'nombre' => '', 'rol' => 'tienda'];
if ($accion === "editar" && $id) {
    $usuario = $usuariosObj->getById($id);
    if (!$usuario) {
        $mensaje_error = "Usuario no encontrado.";
    } else {
        $mostrar_formulario = true;
    }
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_usuario = $_POST['id'] ?? '';
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $rol = $_POST['rol'] ?? 'tienda';
    
    if (empty($id_usuario)) {
        $mensaje_error = "El ID del usuario es obligatorio.";
    } elseif (empty($rol)) {
        $mensaje_error = "Debes seleccionar un rol.";
    } else {
        if ($accion === "crear") {
            if ($usuariosObj->usuarioExiste($id_usuario)) {
                $mensaje_error = "El ID de usuario ya existe.";
            } elseif (empty($password) || empty($password2)) {
                $mensaje_error = "Por favor ingresa la contraseña.";
            } elseif ($password !== $password2) {
                $mensaje_error = "Las contraseñas no coinciden.";
            } else {
                if ($usuariosObj->insertarUsuario($id_usuario, $password, $rol)) {
                    $mensaje = "Usuario creado correctamente.";
                    $mostrar_formulario = false;
                    $listaUsuarios = $usuariosObj->showUsuarios();
                } else {
                    $mensaje_error = "Error al crear el usuario.";
                }
            }
        } elseif ($accion === "editar" && $id) {
            if ($usuariosObj->actualizarUsuario($id, $rol)) {
                // Actualizar contraseña si la proporcionó
                if (!empty($password)) {
                    if ($password !== $password2) {
                        $mensaje_error = "Las contraseñas no coinciden.";
                    } else {
                        $usuariosObj->actualizarContraseña($id, $password);
                        $mensaje = "Usuario actualizado correctamente (contraseña cambiada).";
                    }
                } else {
                    $mensaje = "Usuario actualizado correctamente.";
                }
                $mostrar_formulario = false;
                $listaUsuarios = $usuariosObj->showUsuarios();
            } else {
                $mensaje_error = "Error al actualizar el usuario.";
            }
        }
    }
}

if ($accion === "crear") {
    $mostrar_formulario = true;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PhoneCRM - Gestión de Usuarios</title>
    <link href="../startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/favicon.png">

</head>
<body id="page-top">

    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("./includes/navbar.php"); ?>
                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
                        <h1 class="h3 mb-0 text-gray-800">Gestión de Usuarios</h1>
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

                    <a href="usuarios.php?accion=crear" class="btn btn-primary mb-3">
                        <i class="fas fa-plus"></i> Nuevo Usuario
                    </a>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Usuarios del Sistema</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Rol</th>
                                            <th>Fecha de Creación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($listaUsuarios as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $user['rol'] === 'encargado' ? 'danger' : 'info'; ?>">
                                                    <?php echo ucfirst($user['rol']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($user['fecha_creacion'])); ?></td>
                                            <td>
                                                <a href="usuarios.php?accion=editar&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <?php if ($user['id'] != $_SESSION['usuario_id']): ?>
                                                    <a href="usuarios.php?accion=eliminar&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php if ($mostrar_formulario): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <?php echo $accion === "crear" ? "Crear Nuevo Usuario" : "Editar Usuario"; ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <?php if ($accion === "crear"): ?>
                                <div class="form-group">
                                    <label>ID de Usuario</label>
                                    <input type="number" name="id" class="form-control" placeholder="Ej: 2, 3, 4..." required>
                                </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <label>Rol</label>
                                    <select name="rol" class="form-control" required>
                                        <option value="tienda" <?php echo $usuario['rol'] === 'tienda' ? 'selected' : ''; ?>>Tienda</option>
                                        <option value="encargado" <?php echo $usuario['rol'] === 'encargado' ? 'selected' : ''; ?>>Encargado</option>
                                    </select>
                                </div>

                                <?php if ($accion === "crear"): ?>
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Repetir Contraseña</label>
                                    <input type="password" name="password2" class="form-control" required>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    Déja vacío si no quieres cambiar la contraseña
                                </div>
                                <div class="form-group">
                                    <label>Nueva Contraseña (opcional)</label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Repetir Contraseña (opcional)</label>
                                    <input type="password" name="password2" class="form-control">
                                </div>
                                <?php endif; ?>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo $accion === "crear" ? "Crear" : "Actualizar"; ?>
                                </button>
                                <a href="usuarios.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

            </div>

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
