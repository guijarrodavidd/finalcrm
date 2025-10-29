<?php
require_once("admin/includes/database.php");

class Sessions {
    public function comprobarCredenciales($nombre, $contraseña) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM usuarios WHERE nombre = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $usuario = $result->fetch_assoc();
        $db->closeConnection($conn);
        
        if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
            return $usuario;
        }

        return null;
    }
    
    public function crearSesion($usuario) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
    }
    
    public function comprobarSesion() {
        return isset($_SESSION['usuario_id']);
    }
    
    public function cerrarSesion() {
        session_start();
        session_destroy();
    }
}
?>
