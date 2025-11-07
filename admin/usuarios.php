<?php
session_start();
include("./includes/database.php");
require_once "./includes/crudUsuarios.php";

$connClass = new Connection();
$conexion = $connClass->getConnection();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Solo encargado puede acceder
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

$usuariosObj = new usuarios();
$listaUsuarios = $usuariosObj->showUsuarios();

$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;
$mensaje = "";
$mensaje_error = "";
$mostrar_formulario = false;

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

$usuario = ['id' => '', 'nombre' => '', 'rol' => 'tienda', 'foto' => ''];
if ($accion === "editar" && $id) {
    $usuario = $usuariosObj->getById($id);
    if (!$usuario) {
        $mensaje_error = "Usuario no encontrado.";
    } else {
        $mostrar_formulario = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_usuario = $_POST['id'] ?? '';
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $rol = $_POST['rol'] ?? 'tienda';
    $foto_path = '';
    
    // Procesar foto de perfil
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_nombre = $_FILES['foto']['name'];
        $foto_ext = pathinfo($foto_nombre, PATHINFO_EXTENSION);
        $foto_nuevo_nombre = 'user_' . time() . '.' . $foto_ext;
        $foto_path = '../images/profiles/' . $foto_nuevo_nombre;
        
        if (!is_dir('../images/profiles/')) {
            mkdir('../images/profiles/', 0755, true);
        }
        
        if (move_uploaded_file($foto_tmp, $foto_path)) {
            $foto_path = 'images/profiles/' . $foto_nuevo_nombre;
        } else {
            $mensaje_error = "Error al subir la foto.";
            $foto_path = '';
        }
    }
    
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
                    // Actualizar foto si se subió
                    if (!empty($foto_path)) {
                        $sql_foto = "UPDATE usuarios SET foto = ? WHERE id = ?";
                        $stmt_foto = $conexion->prepare($sql_foto);
                        $stmt_foto->bind_param("si", $foto_path, $id_usuario);
                        $stmt_foto->execute();
                    }
                    $mensaje = "Usuario creado correctamente.";
                    $mostrar_formulario = false;
                    $listaUsuarios = $usuariosObj->showUsuarios();
                } else {
                    $mensaje_error = "Error al crear el usuario.";
                }
            }
        } elseif ($accion === "editar" && $id) {
            if ($usuariosObj->actualizarUsuario($id, $rol)) {
                // Actualizar foto si se subió
                if (!empty($foto_path)) {
                    $sql_foto = "UPDATE usuarios SET foto = ? WHERE id = ?";
                    $stmt_foto = $conexion->prepare($sql_foto);
                    $stmt_foto->bind_param("si", $foto_path, $id);
                    $stmt_foto->execute();
                }
                
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
    <link href="./vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/favicon.png?v=2">
</head>
<body id="page-top">

    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("./includes/navbar_admin.php"); ?>
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
                        <div class="card-header py-3 bg-primary">
                            <h6 class="m-0 font-weight-bold text-white">Usuarios del Sistema</h6>
                        </div>
                        <div class="card-body">
                            <?php if (empty($listaUsuarios)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No hay usuarios registrados</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="text-align:center; width:80px;">Foto</th>
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
                                                <td style="text-align:center; vertical-align:middle;">
                                                    <?php 
                                                    $foto_path = "../" . $user['foto'];
                                                    if (!empty($user['foto']) && file_exists($foto_path)): 
                                                    ?>
                                                        <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="<?php echo htmlspecialchars($user['nombre']); ?>" class="rounded-circle" style="width:50px; height:50px; object-fit:cover; border:2px solid #007bff; background:#fff; padding:2px;">
                                                    <?php else: ?>
                                                        <i class="fas fa-user-circle" style="font-size:50px; color:#007bff;"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['id']); ?></td>
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
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($mostrar_formulario): ?>
                    <div class="card shadow mb-4 border-left-primary">
                        <div class="card-header bg-gradient-primary py-3">
                            <h6 class="m-0 font-weight-bold text-white">
                                <?php echo $accion === "crear" ? "Crear Nuevo Usuario" : "Editar Usuario"; ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <?php if ($accion === "crear"): ?>
                                <div class="form-group">
                                    <label for="id"><strong>ID de Usuario</strong></label>
                                    <input type="number" name="id" id="id" class="form-control form-control-lg" placeholder="Ej: 2, 3, 4..." required>
                                </div>
                                <?php else: ?>
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">
                                <div class="form-group">
                                    <label><strong>ID de Usuario</strong></label>
                                    <input type="text" class="form-control form-control-lg" value="<?php echo htmlspecialchars($usuario['id']); ?>" disabled>
                                </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="rol"><strong>Rol</strong></label>
                                    <select name="rol" id="rol" class="form-control form-control-lg" required>
                                        <option value="tienda" <?php echo ($usuario['rol'] ?? '') === 'tienda' ? 'selected' : ''; ?>>Tienda</option>
                                        <option value="encargado" <?php echo ($usuario['rol'] ?? '') === 'encargado' ? 'selected' : ''; ?>>Encargado</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="foto"><strong>Foto de Perfil</strong></label>
                                    <?php if ($accion === "editar" && !empty($usuario['foto']) && file_exists("../" . $usuario['foto'])): ?>
                                        <div class="mb-3">
                                            <img src="<?php echo "../" . htmlspecialchars($usuario['foto']); ?>" alt="Foto actual" style="width:80px; height:80px; border-radius:50%; border:2px solid #007bff; background:#fff; padding:3px; object-fit:cover;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="foto" id="foto" class="form-control form-control-lg" accept="image/*">
                                    <small class="form-text text-muted">Formatos: JPG, PNG. Máximo 5MB</small>
                                </div>

                                <?php if ($accion === "crear"): ?>
                                <div class="form-group">
                                    <label for="password"><strong>Contraseña</strong></label>
                                    <input type="password" name="password" id="password" class="form-control form-control-lg" required>
                                </div>
                                <div class="form-group">
                                    <label for="password2"><strong>Repetir Contraseña</strong></label>
                                    <input type="password" name="password2" id="password2" class="form-control form-control-lg" required>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Déja vacío si no quieres cambiar la contraseña
                                </div>
                                <div class="form-group">
                                    <label for="password"><strong>Nueva Contraseña (opcional)</strong></label>
                                    <input type="password" name="password" id="password" class="form-control form-control-lg">
                                </div>
                                <div class="form-group">
                                    <label for="password2"><strong>Repetir Contraseña (opcional)</strong></label>
                                    <input type="password" name="password2" id="password2" class="form-control form-control-lg">
                                </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> <?php echo $accion === "crear" ? "Crear Usuario" : "Actualizar Usuario"; ?>
                                    </button>
                                    <a href="usuarios.php" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
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

    <script src="./vendor/jquery/jquery.min.js"></script>
    <script src="./vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
