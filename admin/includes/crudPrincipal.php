<?php

class CrudPrincipal {
    private $conexion;
    private $usuario_id;

    public function __construct($conexion, $usuario_id) {
        $this->conexion = $conexion;
        $this->usuario_id = $usuario_id;
    }

    // Total de clientes
    public function getTotalClientes() {
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE usuario_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    // Total de actividades pendientes
    public function getActividadesPendientes() {
        $sql = "SELECT COUNT(*) as total FROM actividades a 
                JOIN clientes c ON a.cliente_id = c.id 
                WHERE c.usuario_id = ? AND a.completada = 0";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    // Actividades para hoy
    public function getActividadesHoy() {
        $hoy = date('Y-m-d');
        $sql = "SELECT COUNT(*) as total FROM actividades a 
                JOIN clientes c ON a.cliente_id = c.id 
                WHERE c.usuario_id = ? AND DATE(a.fecha) = ? AND a.completada = 0";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("is", $this->usuario_id, $hoy);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    // Clientes visitados recientemente (últimos 10)
    public function getClientesRecientes() {
        $sql = "SELECT id, nombre_apellidos, telefono, ultima_visita 
                FROM clientes 
                WHERE usuario_id = ? AND ultima_visita IS NOT NULL
                ORDER BY ultima_visita DESC 
                LIMIT 10";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Próximas actividades (7 días)
    public function getProximasActividades() {
        $hoy = date('Y-m-d');
        $fecha_limite = date('Y-m-d', strtotime('+7 days'));
        $sql = "SELECT a.id, a.tipo, a.fecha, c.nombre_apellidos, c.id as cliente_id
                FROM actividades a
                JOIN clientes c ON a.cliente_id = c.id
                WHERE c.usuario_id = ? AND a.completada = 0 AND a.fecha BETWEEN ? AND ?
                ORDER BY a.fecha ASC
                LIMIT 10";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iss", $this->usuario_id, $hoy, $fecha_limite);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Actualizar última visita
    public function actualizarUltimaVisita($cliente_id) {
        $sql = "UPDATE clientes SET ultima_visita = NOW() WHERE id = ? AND usuario_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $this->usuario_id);
        return $stmt->execute();
    }
}
?>
