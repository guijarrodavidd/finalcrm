<?php

class usuarios {
    private $conexion;

    public function __construct() {
        require_once "database.php";
        $connClass = new Connection();
        $this->conexion = $connClass->getConnection();
    }

    public function showUsuarios() {
        $sql = "SELECT id, nombre, foto, rol, fecha_creacion FROM usuarios ORDER BY fecha_creacion DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT id, nombre, foto, rol, fecha_creacion FROM usuarios WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function insertarUsuario($id, $password, $rol) {
        // Generar nombre automático basado en ID
        $nombre = "Usuario " . $id;
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (id, nombre, contraseña, rol) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("isss", $id, $nombre, $password_hash, $rol);
        return $stmt->execute();
    }

    public function actualizarUsuario($id, $rol) {
        $sql = "UPDATE usuarios SET rol = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("si", $rol, $id);
        return $stmt->execute();
    }

    public function actualizarContraseña($id, $password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET contraseña = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("si", $password_hash, $id);
        return $stmt->execute();
    }

    public function eliminarUsuario($id) {
        if ($id == $_SESSION['usuario_id']) {
            return false;
        }
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function usuarioExiste($id) {
        $sql = "SELECT id FROM usuarios WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}

?>
