<?php

class crudActividades {
    private $conexion;
    private $usuario_id;

    public function __construct($conexion, $usuario_id) {
        $this->conexion = $conexion;
        $this->usuario_id = $usuario_id;
    }

    // MÉTODOS DE ACTIVIDADES
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

    public function getActividadesUsuario() {
        $sql = "SELECT a.id, a.cliente_id, a.tipo, a.descripcion, a.fecha, a.completada, c.nombre_apellidos, c.convergente
                FROM actividades a
                JOIN clientes c ON a.cliente_id = c.id
                WHERE c.usuario_id = ?
                ORDER BY a.fecha ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getActividadesPendientesUsuario() {
        $sql = "SELECT a.id, a.cliente_id, a.tipo, a.descripcion, a.fecha, a.completada, c.nombre_apellidos, c.convergente
                FROM actividades a
                JOIN clientes c ON a.cliente_id = c.id
                WHERE c.usuario_id = ? AND a.completada = 0
                ORDER BY a.fecha ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getActividadesCompletadasUsuario() {
        $sql = "SELECT a.id, a.cliente_id, a.tipo, a.descripcion, a.fecha, a.completada, c.nombre_apellidos, c.convergente
                FROM actividades a
                JOIN clientes c ON a.cliente_id = c.id
                WHERE c.usuario_id = ? AND a.completada = 1
                ORDER BY a.fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // PAGINACIÓN
    public function getActividadesPaginadas($pagina = 1, $por_pagina = 30) {
        $offset = ($pagina - 1) * $por_pagina;
        $sql = "SELECT a.id, a.cliente_id, a.tipo, a.descripcion, a.fecha, a.completada, c.nombre_apellidos, c.convergente
                FROM actividades a
                JOIN clientes c ON a.cliente_id = c.id
                WHERE c.usuario_id = ?
                ORDER BY a.fecha ASC
                LIMIT ? OFFSET ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iii", $this->usuario_id, $por_pagina, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getActividadesPendientesPaginadas($pagina = 1, $por_pagina = 30) {
        $offset = ($pagina - 1) * $por_pagina;
        $sql = "SELECT a.id, a.cliente_id, a.tipo, a.descripcion, a.fecha, a.completada, c.nombre_apellidos, c.convergente
                FROM actividades a
                JOIN clientes c ON a.cliente_id = c.id
                WHERE c.usuario_id = ? AND a.completada = 0
                ORDER BY a.fecha ASC
                LIMIT ? OFFSET ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iii", $this->usuario_id, $por_pagina, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getActividadesCompletadasPaginadas($pagina = 1, $por_pagina = 30) {
        $offset = ($pagina - 1) * $por_pagina;
        $sql = "SELECT a.id, a.cliente_id, a.tipo, a.descripcion, a.fecha, a.completada, c.nombre_apellidos, c.convergente
                FROM actividades a
                JOIN clientes c ON a.cliente_id = c.id
                WHERE c.usuario_id = ? AND a.completada = 1
                ORDER BY a.fecha DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iii", $this->usuario_id, $por_pagina, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getTotalPaginasActividades($por_pagina = 30) {
        $sql = "SELECT COUNT(*) as total FROM actividades a 
                JOIN clientes c ON a.cliente_id = c.id 
                WHERE c.usuario_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return ceil($resultado['total'] / $por_pagina);
    }

    public function getTotalPaginasActividadesPendientes($por_pagina = 30) {
        $sql = "SELECT COUNT(*) as total FROM actividades a 
                JOIN clientes c ON a.cliente_id = c.id 
                WHERE c.usuario_id = ? AND a.completada = 0";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return ceil($resultado['total'] / $por_pagina);
    }

    public function getTotalPaginasActividadesCompletadas($por_pagina = 30) {
        $sql = "SELECT COUNT(*) as total FROM actividades a 
                JOIN clientes c ON a.cliente_id = c.id 
                WHERE c.usuario_id = ? AND a.completada = 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return ceil($resultado['total'] / $por_pagina);
    }

    // FILTRO POR ETIQUETAS
    public function getActividadesPaginadasPorEtiqueta($etiqueta_id, $filtro, $pagina = 1, $por_pagina = 30) {
        $offset = ($pagina - 1) * $por_pagina;
        
        $where_completada = "";
        if ($filtro === 'pendientes') {
            $where_completada = "AND a.completada = 0";
        } elseif ($filtro === 'completadas') {
            $where_completada = "AND a.completada = 1";
        }
        
        $sql = "SELECT DISTINCT a.id, a.cliente_id, a.tipo, a.descripcion, a.fecha, a.completada, c.nombre_apellidos, c.convergente
                FROM actividades a
                JOIN clientes c ON a.cliente_id = c.id
                JOIN cliente_etiqueta ce ON c.id = ce.cliente_id
                WHERE c.usuario_id = ? AND ce.etiqueta_id = ? $where_completada
                ORDER BY a.fecha ASC
                LIMIT ? OFFSET ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiii", $this->usuario_id, $etiqueta_id, $por_pagina, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getTotalPaginasActividadesEtiqueta($etiqueta_id, $filtro, $por_pagina = 30) {
        $where_completada = "";
        if ($filtro === 'pendientes') {
            $where_completada = "AND a.completada = 0";
        } elseif ($filtro === 'completadas') {
            $where_completada = "AND a.completada = 1";
        }
        
        $sql = "SELECT COUNT(DISTINCT a.id) as total FROM actividades a
                JOIN clientes c ON a.cliente_id = c.id
                JOIN cliente_etiqueta ce ON c.id = ce.cliente_id
                WHERE c.usuario_id = ? AND ce.etiqueta_id = ? $where_completada";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $this->usuario_id, $etiqueta_id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return ceil($resultado['total'] / $por_pagina);
    }
    // Obtener TODAS las etiquetas del sistema (no solo las de actividades)
    public function getTodasEtiquetasSistema() {
        $sql = "SELECT id, nombre, color FROM etiquetas ORDER BY nombre ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->get_result();
    }

}
?>
