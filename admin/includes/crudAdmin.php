<?php

class CrudAdmin {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Total de clientes de TODAS las tiendas
    public function getTotalClientesSistema() {
        $sql = "SELECT COUNT(*) as total FROM clientes";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_assoc()['total'];
    }

    // Total de actividades pendientes de TODAS las tiendas
    public function getActividadesPendientesSistema() {
        $sql = "SELECT COUNT(*) as total FROM actividades WHERE completada = 0";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_assoc()['total'];
    }

    // Actividades para hoy de TODAS las tiendas
    public function getActividadesHoySistema() {
        $hoy = date('Y-m-d');
        $sql = "SELECT COUNT(*) as total FROM actividades 
                WHERE DATE(fecha) = ? AND completada = 0";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $hoy);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    // Clientes visitados recientemente de TODAS las tiendas
    public function getClientesRecientesSistema() {
        $sql = "SELECT id, nombre_apellidos, telefono, ultima_visita 
                FROM clientes 
                WHERE ultima_visita IS NOT NULL
                ORDER BY ultima_visita DESC 
                LIMIT 10";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Próximas actividades de TODAS las tiendas
    public function getProximasActividadesSistema() {
        $hoy = date('Y-m-d');
        $fecha_limite = date('Y-m-d', strtotime('+7 days'));
        $sql = "SELECT a.id, a.tipo, a.fecha, c.nombre_apellidos, c.id as cliente_id
                FROM actividades a
                JOIN clientes c ON a.cliente_id = c.id
                WHERE a.completada = 0 AND a.fecha BETWEEN ? AND ?
                ORDER BY a.fecha ASC
                LIMIT 10";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ss", $hoy, $fecha_limite);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
