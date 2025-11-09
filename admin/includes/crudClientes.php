<?php

class crudClientes {
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

    public function getClienteById($cliente_id) {
        $sql = "SELECT * FROM clientes WHERE id = ? AND usuario_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $this->usuario_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function actualizarCliente($cliente_id, $nombre_apellidos, $dni, $telefono, $convergente) {
        $sql = "UPDATE clientes SET nombre_apellidos = ?, dni = ?, telefono = ?, convergente = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sssiii", $nombre_apellidos, $dni, $telefono, $convergente, $cliente_id, $this->usuario_id);
        return $stmt->execute();
    }

    public function actualizarUltimaVisita($cliente_id) {
        $cliente_id = intval($cliente_id);
        $sql = "UPDATE clientes SET ultima_visita = NOW() WHERE id = ? AND usuario_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $this->usuario_id);
        return $stmt->execute();
    }

    // ✅ PAGINACIÓN SIMPLE Y FUNCIONAL
    public function getClientesPaginados($pagina = 1, $por_pagina = 30) {
        $pagina = max(1, intval($pagina));
        $por_pagina = intval($por_pagina);
        $offset = ($pagina - 1) * $por_pagina;
        
        $sql = "SELECT c.id, c.nombre_apellidos, c.dni, c.telefono, c.convergente, c.fecha_creacion, c.ultima_visita
                FROM clientes c 
                WHERE c.usuario_id = " . intval($this->usuario_id) . " 
                ORDER BY c.fecha_creacion DESC 
                LIMIT " . intval($por_pagina) . " OFFSET " . intval($offset);
        
        return $this->conexion->query($sql);
    }

    public function getTotalPaginasClientes($por_pagina = 30) {
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE usuario_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return ceil($resultado['total'] / $por_pagina);
    }

    public function getEtiquetas($cliente_id) {
        $sql = "SELECT e.id, e.nombre, e.color FROM etiquetas e 
                JOIN cliente_etiqueta ce ON e.id = ce.etiqueta_id 
                WHERE ce.cliente_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getTodasLasEtiquetas() {
        $sql = "SELECT id, nombre, color FROM etiquetas ORDER BY nombre ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getEtiquetasClienteIds($cliente_id) {
        $sql = "SELECT etiqueta_id FROM cliente_etiqueta WHERE cliente_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function eliminarEtiquetasCliente($cliente_id) {
        $sql = "DELETE FROM cliente_etiqueta WHERE cliente_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        return $stmt->execute();
    }

    public function agregarEtiquetaCliente($cliente_id, $etiqueta_id) {
        $sql = "INSERT INTO cliente_etiqueta (cliente_id, etiqueta_id) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $etiqueta_id);
        return $stmt->execute();
    }

    public function getEtiquetasClienteCompletas($cliente_id) {
        $sql = "SELECT e.id, e.nombre, e.color FROM etiquetas e
                JOIN cliente_etiqueta ce ON e.id = ce.etiqueta_id
                WHERE ce.cliente_id = ?
                ORDER BY e.nombre ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getTodasEtiquetasUsuario() {
        $sql = "SELECT DISTINCT e.id, e.nombre, e.color 
                FROM etiquetas e
                JOIN cliente_etiqueta ce ON e.id = ce.etiqueta_id
                JOIN clientes c ON ce.cliente_id = c.id
                WHERE c.usuario_id = ?
                ORDER BY e.nombre ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $this->usuario_id);
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
            return 'danger';
        } elseif ($fecha_actividad == $hoy) {
            return 'success';
        } else {
            return 'secondary';
        }
    }

    public function buscarClientesPaginados($busqueda, $pagina = 1, $por_pagina = 30) {
        $pagina = max(1, intval($pagina));
        $por_pagina = intval($por_pagina);
        $offset = ($pagina - 1) * $por_pagina;
        $busqueda = $this->conexion->real_escape_string($busqueda);
        
        $sql = "SELECT c.id, c.nombre_apellidos, c.dni, c.telefono, c.convergente, c.fecha_creacion, c.ultima_visita
                FROM clientes c 
                WHERE c.usuario_id = " . intval($this->usuario_id) . " AND c.nombre_apellidos LIKE '%$busqueda%'
                ORDER BY c.fecha_creacion DESC 
                LIMIT " . intval($por_pagina) . " OFFSET " . intval($offset);
        
        return $this->conexion->query($sql);
    }

    public function getTotalPaginasBusqueda($busqueda, $por_pagina = 30) {
        $busqueda = $this->conexion->real_escape_string($busqueda);
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE usuario_id = " . intval($this->usuario_id) . " AND nombre_apellidos LIKE '%$busqueda%'";
        $resultado = $this->conexion->query($sql);
        $row = $resultado->fetch_assoc();
        return ceil($row['total'] / $por_pagina);
    }

    public function getClientesPaginadosPorEtiqueta($etiqueta_id, $pagina = 1, $por_pagina = 30) {
        $pagina = max(1, intval($pagina));
        $por_pagina = intval($por_pagina);
        $offset = ($pagina - 1) * $por_pagina;
        $etiqueta_id = intval($etiqueta_id);
        
        $sql = "SELECT DISTINCT c.id, c.nombre_apellidos, c.dni, c.telefono, c.convergente, c.fecha_creacion, c.ultima_visita
                FROM clientes c
                JOIN cliente_etiqueta ce ON c.id = ce.cliente_id
                WHERE c.usuario_id = " . intval($this->usuario_id) . " AND ce.etiqueta_id = $etiqueta_id
                ORDER BY c.fecha_creacion DESC 
                LIMIT " . intval($por_pagina) . " OFFSET " . intval($offset);
        
        return $this->conexion->query($sql);
    }

    public function getTotalPaginasClientesEtiqueta($etiqueta_id, $por_pagina = 30) {
        $etiqueta_id = intval($etiqueta_id);
        $sql = "SELECT COUNT(DISTINCT c.id) as total FROM clientes c
                JOIN cliente_etiqueta ce ON c.id = ce.cliente_id
                WHERE c.usuario_id = " . intval($this->usuario_id) . " AND ce.etiqueta_id = $etiqueta_id";
        $resultado = $this->conexion->query($sql);
        $row = $resultado->fetch_assoc();
        return ceil($row['total'] / $por_pagina);
    }

    public function getTodasTiendas() {
        $sql = "SELECT id, nombre FROM usuarios WHERE rol = 'tienda' ORDER BY nombre ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getClientesTiendaPaginados($tienda_id, $pagina = 1, $por_pagina = 30) {
        $pagina = max(1, intval($pagina));
        $por_pagina = intval($por_pagina);
        $offset = ($pagina - 1) * $por_pagina;
        $tienda_id = intval($tienda_id);
        
        $sql = "SELECT c.id, c.nombre_apellidos, c.dni, c.telefono, c.convergente, c.fecha_creacion, c.ultima_visita
                FROM clientes c 
                WHERE c.usuario_id = $tienda_id
                ORDER BY c.fecha_creacion DESC 
                LIMIT " . intval($por_pagina) . " OFFSET " . intval($offset);
        
        return $this->conexion->query($sql);
    }

    public function getTotalPaginasTienda($tienda_id, $por_pagina = 30) {
        $tienda_id = intval($tienda_id);
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE usuario_id = $tienda_id";
        $resultado = $this->conexion->query($sql);
        $row = $resultado->fetch_assoc();
        return ceil($row['total'] / $por_pagina);
    }

    public function getTotalClientesTienda($tienda_id) {
        $tienda_id = intval($tienda_id);
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE usuario_id = $tienda_id";
        $resultado = $this->conexion->query($sql);
        $row = $resultado->fetch_assoc();
        return $row['total'];
    }

    public function getClientesSistemaPaginados($pagina = 1, $por_pagina = 30) {
        $pagina = max(1, intval($pagina));
        $por_pagina = intval($por_pagina);
        $offset = ($pagina - 1) * $por_pagina;
        
        $sql = "SELECT c.id, c.nombre_apellidos, c.dni, c.telefono, c.convergente, c.fecha_creacion, c.usuario_id, u.nombre as tienda_nombre, c.ultima_visita
                FROM clientes c
                JOIN usuarios u ON c.usuario_id = u.id
                ORDER BY c.fecha_creacion DESC 
                LIMIT " . intval($por_pagina) . " OFFSET " . intval($offset);
        
        return $this->conexion->query($sql);
    }

    public function getTotalPaginasSistema($por_pagina = 30) {
        $sql = "SELECT COUNT(*) as total FROM clientes";
        $resultado = $this->conexion->query($sql);
        $row = $resultado->fetch_assoc();
        return ceil($row['total'] / $por_pagina);
    }

    public function getTotalClientesSistema() {
        $sql = "SELECT COUNT(*) as total FROM clientes";
        $resultado = $this->conexion->query($sql);
        $row = $resultado->fetch_assoc();
        return $row['total'];
    }

    public function eliminarCliente($cliente_id) {
        $cliente_id = intval($cliente_id);
        $sql = "DELETE FROM clientes WHERE id = $cliente_id AND usuario_id = " . intval($this->usuario_id);
        return $this->conexion->query($sql);
    }

    public function insertarCliente($nombre_apellidos, $dni, $telefono, $convergente = 0) {
        $sql = "INSERT INTO clientes (nombre_apellidos, dni, telefono, convergente, usuario_id, fecha_creacion) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sssii", $nombre_apellidos, $dni, $telefono, $convergente, $this->usuario_id);
        return $stmt->execute();
    }
}
?>
