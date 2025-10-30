<?php

class CrudClientes {
    private $conexion;
    private $usuario_id;

    public function __construct($conexion, $usuario_id) {
        $this->conexion = $conexion;
        $this->usuario_id = $usuario_id;
    }

    public function getTotalClientes() {
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE usuario_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc()['total'];
    }

    public function getClientes() {
        $sql = "SELECT c.id, c.nombre_apellidos, c.dni, c.telefono, c.convergente, c.fecha_creacion 
                FROM clientes c 
                WHERE c.usuario_id = ? 
                ORDER BY c.fecha_creacion DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function agregarActividad($cliente_id, $tipo, $descripcion, $fecha) {
        $sql = "INSERT INTO actividades (cliente_id, tipo, descripcion, fecha, completada) VALUES (?, ?, ?, ?, 0)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("isss", $cliente_id, $tipo, $descripcion, $fecha);
        return $stmt->execute();
    }

    public function marcarCompletada($actividad_id, $estado) {
        $sql = "UPDATE actividades SET completada = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $estado, $actividad_id);
        return $stmt->execute();
    }

    public function getProximaActividad($cliente_id) {
        $sql = "SELECT id, tipo, fecha, completada FROM actividades 
                WHERE cliente_id = ? AND completada = 0
                ORDER BY fecha ASC LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getActividades($cliente_id) {
        $sql = "SELECT id, tipo, descripcion, fecha, completada FROM actividades 
                WHERE cliente_id = ? 
                ORDER BY fecha DESC LIMIT 10";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getEtiquetas($cliente_id) {
        $sql = "SELECT e.nombre, e.color FROM etiquetas e 
                JOIN cliente_etiqueta ce ON e.id = ce.etiqueta_id 
                WHERE ce.cliente_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public static function getColorActividad($fecha, $completada) {
        if ($completada) {
            return 'completada';
        }
        
        $hoy = date('Y-m-d');
        $fecha_actividad = date('Y-m-d', strtotime($fecha));
        
        if ($fecha_actividad < $hoy) {
            return 'pasada';
        } elseif ($fecha_actividad == $hoy) {
            return 'hoy';
        } else {
            return 'futura';
        }
    }
}
?>
