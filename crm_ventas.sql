-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-10-2025 a las 20:45:44
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `crm_ventas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `tipo` enum('Llamada','WhatsApp','Cita','Revisión') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id`, `cliente_id`, `tipo`, `descripcion`, `fecha`, `fecha_creacion`) VALUES
(1, 1, 'Llamada', 'Confirmar instalación técnico', '2025-10-28', '2025-10-26 19:43:01'),
(2, 2, 'WhatsApp', 'Enviar documentación necesaria para portabilidad', '2025-10-27', '2025-10-26 19:43:01'),
(3, 9, 'Cita', 'Visita a domicilio para verificar cobertura', '2025-10-30', '2025-10-26 19:43:01'),
(4, 3, 'Revisión', 'Seguimiento post-venta primer mes', '2025-11-20', '2025-10-26 19:43:01'),
(5, 4, 'Llamada', 'Proponer ampliación a línea adicional', '2025-10-29', '2025-10-26 19:43:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre_apellidos` varchar(150) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `notas` text DEFAULT NULL,
  `convergente` text DEFAULT NULL,
  `etiqueta_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre_apellidos`, `dni`, `telefono`, `notas`, `convergente`, `etiqueta_id`, `usuario_id`, `fecha_creacion`) VALUES
(1, 'Juan García Pérez', '12345678A', '600123456', 'Cliente interesado en mejorar su tarifa actual', 'Fibra 600Mb + 2 líneas móviles 50GB', 6, 1, '2025-10-26 19:43:01'),
(2, 'María López Sánchez', '23456789B', '611234567', 'Quiere cambiar de Vodafone a Movistar', 'Fibra 1Gb + 3 líneas móviles ilimitadas', 7, 8778, '2025-10-26 19:43:01'),
(3, 'Carlos Martínez Ruiz', '34567890C', '622345678', 'Nuevo cliente, primera contratación', 'Fibra 300Mb + 1 línea móvil 30GB', 12, 8779, '2025-10-26 19:43:01'),
(4, 'Ana Fernández Torres', '45678901D', '633456789', 'Cliente recurrente, muy satisfecho', 'Fibra 600Mb + 4 líneas móviles 100GB', 9, 8780, '2025-10-26 19:43:01'),
(5, 'Pedro Rodríguez Gómez', '56789012E', '644567890', 'Pendiente de instalación', 'Fibra 1Gb + 2 líneas móviles 80GB', 10, 8781, '2025-10-26 19:43:01'),
(6, 'Laura Jiménez Moreno', '67890123F', '655678901', 'Solicita factura electrónica', 'Fibra 300Mb + 1 línea móvil 50GB', 21, 8782, '2025-10-26 19:43:01'),
(7, 'Francisco Navarro Díaz', '78901234G', '666789012', 'Interesado en contratar luz también', 'Fibra 600Mb + 2 líneas móviles 60GB', 15, 8778, '2025-10-26 19:43:01'),
(8, 'Isabel Romero Castro', '89012345H', '677890123', 'Cliente VIP, atención prioritaria', 'Fibra 1Gb + 5 líneas móviles ilimitadas', 6, 8779, '2025-10-26 19:43:01'),
(9, 'Miguel Ángel Herrera Vega', '90123456I', '688901234', 'Llamar para confirmar instalación el 30/10', 'Fibra 300Mb + 2 líneas móviles 40GB', 13, 8780, '2025-10-26 19:43:01'),
(10, 'Carmen Molina Serrano', '01234567J', '699012345', 'Derivada desde atención al cliente', 'Fibra 600Mb + 3 líneas móviles 70GB', 11, 1, '2025-10-26 19:43:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas`
--

CREATE TABLE `etiquetas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `color` varchar(7) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `etiquetas`
--

INSERT INTO `etiquetas` (`id`, `nombre`, `color`, `fecha_creacion`) VALUES
(1, 'RENTIK', '#000000', '2025-10-26 19:21:33'),
(2, 'FIBRA', '#8B4513', '2025-10-26 19:21:33'),
(3, 'BONO SOCIAL', '#FF0000', '2025-10-26 19:21:33'),
(4, 'LUZ', '#FFFF00', '2025-10-26 19:21:33'),
(5, 'YOIGO', '#800080', '2025-10-26 19:21:33'),
(6, 'MOVISTAR', '#0070C0', '2025-10-26 19:21:33'),
(7, 'VODAFONE', '#E60000', '2025-10-26 19:21:33'),
(8, 'FINETWORK', '#000000', '2025-10-26 19:21:33'),
(9, 'JAZZTEL', '#FF6600', '2025-10-26 19:21:33'),
(10, 'MASMOVIL', '#FFD700', '2025-10-26 19:21:33'),
(11, 'BUTIK', '#FFC0CB', '2025-10-26 19:21:33'),
(12, 'ORANGE', '#FF6600', '2025-10-26 19:21:33'),
(13, 'LOWI', '#FF0000', '2025-10-26 19:21:33'),
(14, 'PEPEPHONE', '#FF0000', '2025-10-26 19:21:33'),
(15, 'IBERDROLA', '#00A651', '2025-10-26 19:21:33'),
(16, 'REPSOL', '#FF6600', '2025-10-26 19:21:33'),
(17, 'NATURGI', '#00A651', '2025-10-26 19:21:33'),
(18, 'ENDESA', '#FFD700', '2025-10-26 19:21:33'),
(19, 'TOTALENERGI', '#0070C0', '2025-10-26 19:21:33'),
(20, 'OTROS', '#808080', '2025-10-26 19:21:33'),
(21, 'DIGI', '#0070C0', '2025-10-26 19:21:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `contraseña` varchar(100) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `contraseña`, `fecha_creacion`) VALUES
(1, 'Phone House Admin', 'AdminPhoneHouse', '2025-10-26 19:21:32'),
(8778, 'Phone House Quart', 'Quart8778', '2025-10-26 19:21:32'),
(8779, 'Phone House Manises', 'Manises8779', '2025-10-26 19:21:32'),
(8780, 'Phone House Paterna', 'Paterna8780', '2025-10-26 19:21:32'),
(8781, 'Phone House Mislata', 'Mislata8781', '2025-10-26 19:21:32'),
(8782, 'Phone House Torrent', 'Torrent8782', '2025-10-26 19:21:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` date NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `cliente_id`, `usuario_id`, `descripcion`, `fecha`, `fecha_creacion`) VALUES
(1, 1, 1, 'Contratación Movistar Fusión 600Mb', '2025-10-15', '2025-10-26 19:43:01'),
(2, 2, 8778, 'Portabilidad Vodafone One 1Gb + TV', '2025-10-18', '2025-10-26 19:43:01'),
(3, 3, 8779, 'Alta nueva Orange Love 300Mb', '2025-10-20', '2025-10-26 19:43:01'),
(4, 4, 8780, 'Ampliación Jazztel Fibra 600Mb + línea adicional', '2025-10-22', '2025-10-26 19:43:01'),
(5, 7, 8778, 'Contratación Iberdrola Luz + Fibra', '2025-10-24', '2025-10-26 19:43:01');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_actividades_cliente` (`cliente_id`),
  ADD KEY `idx_actividades_fecha` (`fecha`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_clientes_usuario` (`usuario_id`),
  ADD KEY `idx_clientes_etiqueta` (`etiqueta_id`);

--
-- Indices de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_ventas_cliente` (`cliente_id`),
  ADD KEY `idx_ventas_fecha` (`fecha`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`etiqueta_id`) REFERENCES `etiquetas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `clientes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
