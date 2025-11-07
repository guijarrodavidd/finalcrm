-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 07-11-2025 a las 13:53:57
-- Versión del servidor: 11.4.8-MariaDB-ubu2404
-- Versión de PHP: 8.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `davidguijarro_FINAL_GUIJARROCANO_DAVID`
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
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `completada` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id`, `cliente_id`, `tipo`, `descripcion`, `fecha`, `fecha_creacion`, `completada`) VALUES
(1, 1, 'Llamada', 'Confirmar instalación técnico', '2025-10-28', '2025-10-26 19:43:01', 0),
(2, 2, 'WhatsApp', 'Enviar documentación necesaria para portabilidad', '2025-10-27', '2025-10-26 19:43:01', 0),
(3, 9, 'Cita', 'Visita a domicilio para verificar cobertura', '2025-10-30', '2025-10-26 19:43:01', 0),
(4, 3, 'Revisión', 'Seguimiento post-venta primer mes', '2025-11-20', '2025-10-26 19:43:01', 1),
(5, 4, 'Llamada', 'Proponer ampliación a línea adicional', '2025-10-29', '2025-10-26 19:43:01', 0),
(6, 14, 'Llamada', 'NC 29/10', '2025-10-30', '2025-10-29 09:43:30', 0),
(7, 11, 'Cita', 'Se pasa hoy', '2025-10-29', '2025-10-29 09:44:21', 1),
(8, 11, 'Llamada', 'No se ha pasado, llamar mañana', '2025-10-30', '2025-10-29 10:21:54', 1),
(9, 12, 'Cita', 'Se pasa hoy por la tarde para revisar su tarifa de Masmovil', '2025-10-29', '2025-10-29 16:06:46', 1),
(10, 12, 'Llamada', 'No se ha pasado, rellamar mañana', '2025-10-30', '2025-10-29 16:07:08', 1),
(11, 3, 'Llamada', 'El cliente está contento con sus servicios, llamar el mes que viene para agendar una cita.', '2025-11-29', '2025-10-29 16:31:21', 0),
(12, 11, 'Llamada', 'llamar', '2025-10-27', '2025-10-29 18:01:06', 0),
(13, 14, 'Llamada', 'fulyfuol', '2025-11-05', '2025-10-31 08:31:59', 0),
(14, 14, 'Llamada', 'zgjrstj', '2025-10-31', '2025-10-31 08:32:28', 0),
(15, 16, 'Revisión', 'Revision de prostata', '2025-11-03', '2025-11-03 08:50:34', 0),
(16, 12, 'Llamada', 'NC 03/11', '2025-11-04', '2025-11-03 19:08:34', 0),
(17, 3, 'Llamada', 'Llamada de seguimiento al cliente Carlos Martínez Ruiz', '2025-11-03', '2025-11-04 11:27:10', 1),
(18, 8, 'Llamada', 'Llamada de seguimiento al cliente Isabel Romero Castro', '2025-10-31', '2025-11-04 11:27:10', 1),
(19, 12, 'Llamada', 'Llamada de seguimiento al cliente Jorge Monzonís', '2025-10-29', '2025-11-04 11:27:10', 0),
(20, 17, 'Llamada', 'Llamada de seguimiento al cliente Cliente 1 García López', '2025-11-01', '2025-11-04 11:27:10', 0),
(21, 18, 'Llamada', 'Llamada de seguimiento al cliente Cliente 2 Martínez Ruiz', '2025-11-01', '2025-11-04 11:27:10', 1),
(22, 19, 'Llamada', 'Llamada de seguimiento al cliente Cliente 3 Fernández Díaz', '2025-11-06', '2025-11-04 11:27:10', 0),
(23, 20, 'Llamada', 'Llamada de seguimiento al cliente Cliente 4 González Pérez', '2025-10-30', '2025-11-04 11:27:10', 1),
(24, 21, 'Llamada', 'Llamada de seguimiento al cliente Cliente 5 Rodríguez Sánchez', '2025-10-29', '2025-11-04 11:27:10', 0),
(25, 22, 'Llamada', 'Llamada de seguimiento al cliente Cliente 6 López Torres', '2025-11-04', '2025-11-04 11:27:10', 1),
(26, 23, 'Llamada', 'Llamada de seguimiento al cliente Cliente 7 Moreno Castro', '2025-11-03', '2025-11-04 11:27:10', 0),
(27, 24, 'Llamada', 'Llamada de seguimiento al cliente Cliente 8 Jiménez Romero', '2025-11-05', '2025-11-04 11:27:10', 1),
(28, 25, 'Llamada', 'Llamada de seguimiento al cliente Cliente 9 Ramírez Domínguez', '2025-11-03', '2025-11-04 11:27:10', 1),
(29, 26, 'Llamada', 'Llamada de seguimiento al cliente Cliente 10 Álvarez Gómez', '2025-11-10', '2025-11-04 11:27:10', 1),
(30, 27, 'Llamada', 'Llamada de seguimiento al cliente Cliente 11 Hernández Navarro', '2025-11-06', '2025-11-04 11:27:10', 0),
(31, 28, 'Llamada', 'Llamada de seguimiento al cliente Cliente 12 García Vargas', '2025-11-03', '2025-11-04 11:27:10', 1),
(32, 29, 'Llamada', 'Llamada de seguimiento al cliente Cliente 13 Martínez Lara', '2025-11-09', '2025-11-04 11:27:10', 1),
(33, 30, 'Llamada', 'Llamada de seguimiento al cliente Cliente 14 Fernández Silva', '2025-11-04', '2025-11-04 11:27:10', 0),
(34, 31, 'Llamada', 'Llamada de seguimiento al cliente Cliente 15 González Fuentes', '2025-11-07', '2025-11-04 11:27:10', 0),
(35, 32, 'Llamada', 'Llamada de seguimiento al cliente Cliente 16 Rodríguez Campos', '2025-11-06', '2025-11-04 11:27:10', 0),
(36, 33, 'Llamada', 'Llamada de seguimiento al cliente Cliente 17 López Guerrero', '2025-10-29', '2025-11-04 11:27:10', 0),
(37, 34, 'Llamada', 'Llamada de seguimiento al cliente Cliente 18 Moreno Espinosa', '2025-10-30', '2025-11-04 11:27:10', 1),
(38, 35, 'Llamada', 'Llamada de seguimiento al cliente Cliente 19 Jiménez Velasco', '2025-11-08', '2025-11-04 11:27:10', 0),
(39, 36, 'Llamada', 'Llamada de seguimiento al cliente Cliente 20 Ramírez Reyes', '2025-10-29', '2025-11-04 11:27:10', 0),
(40, 37, 'Llamada', 'Llamada de seguimiento al cliente Cliente 21 Álvarez Ochoa', '2025-10-29', '2025-11-04 11:27:10', 1),
(41, 38, 'Llamada', 'Llamada de seguimiento al cliente Cliente 22 Hernández Burgos', '2025-10-30', '2025-11-04 11:27:10', 1),
(42, 39, 'Llamada', 'Llamada de seguimiento al cliente Cliente 23 García Ramos', '2025-11-10', '2025-11-04 11:27:10', 1),
(43, 40, 'Llamada', 'Llamada de seguimiento al cliente Cliente 24 Martínez Costa', '2025-11-07', '2025-11-04 11:27:10', 1),
(44, 41, 'Llamada', 'Llamada de seguimiento al cliente Cliente 25 Fernández Molina', '2025-10-29', '2025-11-04 11:27:10', 0),
(45, 42, 'Llamada', 'Llamada de seguimiento al cliente Cliente 26 González Duque', '2025-10-28', '2025-11-04 11:27:10', 1),
(46, 43, 'Llamada', 'Llamada de seguimiento al cliente Cliente 27 Rodríguez Cortés', '2025-10-29', '2025-11-04 11:27:10', 1),
(47, 44, 'Llamada', 'Llamada de seguimiento al cliente Cliente 28 López Saldaña', '2025-11-07', '2025-11-04 11:27:10', 1),
(48, 45, 'Llamada', 'Llamada de seguimiento al cliente Cliente 29 Moreno Santana', '2025-11-03', '2025-11-04 11:27:10', 1),
(49, 46, 'Llamada', 'Llamada de seguimiento al cliente Cliente 30 Jiménez Nolasco', '2025-11-05', '2025-11-04 11:27:10', 0),
(50, 47, 'Llamada', 'Llamada de seguimiento al cliente Cliente 31 Ramírez Quintero', '2025-10-30', '2025-11-04 11:27:10', 0),
(51, 48, 'Llamada', 'Llamada de seguimiento al cliente Cliente 32 Álvarez Pinto', '2025-10-31', '2025-11-04 11:27:10', 1),
(52, 49, 'Llamada', 'Llamada de seguimiento al cliente Cliente 33 Hernández Mena', '2025-11-09', '2025-11-04 11:27:10', 1),
(53, 50, 'Llamada', 'Llamada de seguimiento al cliente Cliente 34 García Cano', '2025-10-28', '2025-11-04 11:27:10', 1),
(54, 51, 'Llamada', 'Llamada de seguimiento al cliente Cliente 35 Martínez Gómez', '2025-10-29', '2025-11-04 11:27:10', 1),
(55, 52, 'Llamada', 'Llamada de seguimiento al cliente Cliente 36 Fernández Ruiz', '2025-11-01', '2025-11-04 11:27:10', 0),
(56, 53, 'Llamada', 'Llamada de seguimiento al cliente Cliente 37 González López', '2025-11-01', '2025-11-04 11:27:10', 1),
(57, 54, 'Llamada', 'Llamada de seguimiento al cliente Cliente 38 Rodríguez García', '2025-11-05', '2025-11-04 11:27:10', 0),
(58, 55, 'Llamada', 'Llamada de seguimiento al cliente Cliente 39 López Díaz', '2025-10-29', '2025-11-04 11:27:10', 0),
(59, 56, 'Llamada', 'Llamada de seguimiento al cliente Cliente 40 Moreno Pérez', '2025-11-08', '2025-11-04 11:27:10', 1),
(60, 57, 'Llamada', 'Llamada de seguimiento al cliente Cliente 41 Jiménez Sánchez', '2025-10-29', '2025-11-04 11:27:10', 1),
(61, 58, 'Llamada', 'Llamada de seguimiento al cliente Cliente 42 Ramírez Torres', '2025-10-30', '2025-11-04 11:27:10', 1),
(62, 59, 'Llamada', 'Llamada de seguimiento al cliente Cliente 43 Álvarez Castro', '2025-11-07', '2025-11-04 11:27:10', 0),
(63, 60, 'Llamada', 'Llamada de seguimiento al cliente Cliente 44 Hernández Romero', '2025-11-03', '2025-11-04 11:27:10', 0),
(64, 61, 'Llamada', 'Llamada de seguimiento al cliente Cliente 45 García Domínguez', '2025-11-01', '2025-11-04 11:27:10', 1),
(65, 62, 'Llamada', 'Llamada de seguimiento al cliente Cliente 46 Martínez Navarro', '2025-10-28', '2025-11-04 11:27:10', 1),
(66, 63, 'Llamada', 'Llamada de seguimiento al cliente Cliente 47 Fernández Gómez', '2025-10-28', '2025-11-04 11:27:10', 1),
(67, 64, 'Llamada', 'Llamada de seguimiento al cliente Cliente 48 González Vargas', '2025-11-06', '2025-11-04 11:27:10', 0),
(68, 65, 'Llamada', 'Llamada de seguimiento al cliente Cliente 49 Rodríguez Lara', '2025-10-30', '2025-11-04 11:27:10', 1),
(69, 66, 'Llamada', 'Llamada de seguimiento al cliente Cliente 50 López Silva', '2025-10-29', '2025-11-04 11:27:10', 1),
(70, 67, 'Llamada', 'Llamada de seguimiento al cliente Cliente 51 Moreno Fuentes', '2025-10-30', '2025-11-04 11:27:10', 0),
(71, 68, 'Llamada', 'Llamada de seguimiento al cliente Cliente 52 Jiménez Campos', '2025-11-08', '2025-11-04 11:27:10', 1),
(72, 69, 'Llamada', 'Llamada de seguimiento al cliente Cliente 53 Ramírez Guerrero', '2025-11-10', '2025-11-04 11:27:10', 1),
(73, 70, 'Llamada', 'Llamada de seguimiento al cliente Cliente 54 Álvarez Espinosa', '2025-11-10', '2025-11-04 11:27:10', 0),
(74, 71, 'Llamada', 'Llamada de seguimiento al cliente Cliente 55 Hernández Velasco', '2025-10-30', '2025-11-04 11:27:10', 1),
(75, 72, 'Llamada', 'Llamada de seguimiento al cliente Cliente 56 García Reyes', '2025-11-01', '2025-11-04 11:27:10', 0),
(76, 73, 'Llamada', 'Llamada de seguimiento al cliente Cliente 57 Martínez Ochoa', '2025-11-02', '2025-11-04 11:27:10', 0),
(77, 74, 'Llamada', 'Llamada de seguimiento al cliente Cliente 58 Fernández Burgos', '2025-10-30', '2025-11-04 11:27:10', 1),
(78, 75, 'Llamada', 'Llamada de seguimiento al cliente Cliente 59 González Ramos', '2025-11-09', '2025-11-04 11:27:10', 0),
(79, 76, 'Llamada', 'Llamada de seguimiento al cliente Cliente 60 Rodríguez Costa', '2025-11-01', '2025-11-04 11:27:10', 0),
(80, 3, 'WhatsApp', 'Mensaje WhatsApp a Carlos Martínez Ruiz', '2025-11-02', '2025-11-04 11:27:10', 1),
(81, 8, 'WhatsApp', 'Mensaje WhatsApp a Isabel Romero Castro', '2025-11-10', '2025-11-04 11:27:10', 0),
(82, 12, 'WhatsApp', 'Mensaje WhatsApp a Jorge Monzonís', '2025-10-29', '2025-11-04 11:27:10', 1),
(83, 17, 'WhatsApp', 'Mensaje WhatsApp a Cliente 1 García López', '2025-11-06', '2025-11-04 11:27:10', 1),
(84, 18, 'WhatsApp', 'Mensaje WhatsApp a Cliente 2 Martínez Ruiz', '2025-11-05', '2025-11-04 11:27:10', 1),
(85, 19, 'WhatsApp', 'Mensaje WhatsApp a Cliente 3 Fernández Díaz', '2025-11-06', '2025-11-04 11:27:10', 1),
(86, 20, 'WhatsApp', 'Mensaje WhatsApp a Cliente 4 González Pérez', '2025-10-28', '2025-11-04 11:27:10', 1),
(87, 21, 'WhatsApp', 'Mensaje WhatsApp a Cliente 5 Rodríguez Sánchez', '2025-11-10', '2025-11-04 11:27:10', 0),
(88, 22, 'WhatsApp', 'Mensaje WhatsApp a Cliente 6 López Torres', '2025-11-03', '2025-11-04 11:27:10', 0),
(89, 23, 'WhatsApp', 'Mensaje WhatsApp a Cliente 7 Moreno Castro', '2025-11-10', '2025-11-04 11:27:10', 0),
(90, 24, 'WhatsApp', 'Mensaje WhatsApp a Cliente 8 Jiménez Romero', '2025-10-29', '2025-11-04 11:27:10', 0),
(91, 25, 'WhatsApp', 'Mensaje WhatsApp a Cliente 9 Ramírez Domínguez', '2025-10-30', '2025-11-04 11:27:10', 1),
(92, 26, 'WhatsApp', 'Mensaje WhatsApp a Cliente 10 Álvarez Gómez', '2025-11-01', '2025-11-04 11:27:10', 1),
(93, 27, 'WhatsApp', 'Mensaje WhatsApp a Cliente 11 Hernández Navarro', '2025-10-29', '2025-11-04 11:27:10', 1),
(94, 28, 'WhatsApp', 'Mensaje WhatsApp a Cliente 12 García Vargas', '2025-11-10', '2025-11-04 11:27:10', 0),
(95, 29, 'WhatsApp', 'Mensaje WhatsApp a Cliente 13 Martínez Lara', '2025-11-02', '2025-11-04 11:27:10', 0),
(96, 30, 'WhatsApp', 'Mensaje WhatsApp a Cliente 14 Fernández Silva', '2025-11-04', '2025-11-04 11:27:10', 0),
(97, 31, 'WhatsApp', 'Mensaje WhatsApp a Cliente 15 González Fuentes', '2025-11-02', '2025-11-04 11:27:10', 0),
(98, 32, 'WhatsApp', 'Mensaje WhatsApp a Cliente 16 Rodríguez Campos', '2025-11-05', '2025-11-04 11:27:10', 1),
(99, 33, 'WhatsApp', 'Mensaje WhatsApp a Cliente 17 López Guerrero', '2025-10-28', '2025-11-04 11:27:10', 0),
(100, 34, 'WhatsApp', 'Mensaje WhatsApp a Cliente 18 Moreno Espinosa', '2025-11-08', '2025-11-04 11:27:10', 1),
(101, 35, 'WhatsApp', 'Mensaje WhatsApp a Cliente 19 Jiménez Velasco', '2025-11-10', '2025-11-04 11:27:10', 0),
(102, 36, 'WhatsApp', 'Mensaje WhatsApp a Cliente 20 Ramírez Reyes', '2025-11-03', '2025-11-04 11:27:10', 0),
(103, 37, 'WhatsApp', 'Mensaje WhatsApp a Cliente 21 Álvarez Ochoa', '2025-11-02', '2025-11-04 11:27:10', 0),
(104, 38, 'WhatsApp', 'Mensaje WhatsApp a Cliente 22 Hernández Burgos', '2025-11-07', '2025-11-04 11:27:10', 1),
(105, 39, 'WhatsApp', 'Mensaje WhatsApp a Cliente 23 García Ramos', '2025-11-08', '2025-11-04 11:27:10', 0),
(106, 40, 'WhatsApp', 'Mensaje WhatsApp a Cliente 24 Martínez Costa', '2025-11-08', '2025-11-04 11:27:10', 0),
(107, 41, 'WhatsApp', 'Mensaje WhatsApp a Cliente 25 Fernández Molina', '2025-11-06', '2025-11-04 11:27:10', 0),
(108, 42, 'WhatsApp', 'Mensaje WhatsApp a Cliente 26 González Duque', '2025-11-09', '2025-11-04 11:27:10', 1),
(109, 43, 'WhatsApp', 'Mensaje WhatsApp a Cliente 27 Rodríguez Cortés', '2025-10-31', '2025-11-04 11:27:10', 0),
(110, 44, 'WhatsApp', 'Mensaje WhatsApp a Cliente 28 López Saldaña', '2025-10-31', '2025-11-04 11:27:10', 0),
(111, 45, 'WhatsApp', 'Mensaje WhatsApp a Cliente 29 Moreno Santana', '2025-11-01', '2025-11-04 11:27:10', 0),
(112, 46, 'WhatsApp', 'Mensaje WhatsApp a Cliente 30 Jiménez Nolasco', '2025-11-03', '2025-11-04 11:27:10', 1),
(113, 47, 'WhatsApp', 'Mensaje WhatsApp a Cliente 31 Ramírez Quintero', '2025-10-30', '2025-11-04 11:27:10', 0),
(114, 48, 'WhatsApp', 'Mensaje WhatsApp a Cliente 32 Álvarez Pinto', '2025-11-02', '2025-11-04 11:27:10', 1),
(115, 49, 'WhatsApp', 'Mensaje WhatsApp a Cliente 33 Hernández Mena', '2025-11-02', '2025-11-04 11:27:10', 0),
(116, 50, 'WhatsApp', 'Mensaje WhatsApp a Cliente 34 García Cano', '2025-10-30', '2025-11-04 11:27:10', 0),
(117, 51, 'WhatsApp', 'Mensaje WhatsApp a Cliente 35 Martínez Gómez', '2025-10-29', '2025-11-04 11:27:10', 1),
(118, 52, 'WhatsApp', 'Mensaje WhatsApp a Cliente 36 Fernández Ruiz', '2025-11-06', '2025-11-04 11:27:10', 0),
(119, 53, 'WhatsApp', 'Mensaje WhatsApp a Cliente 37 González López', '2025-11-01', '2025-11-04 11:27:10', 0),
(120, 54, 'WhatsApp', 'Mensaje WhatsApp a Cliente 38 Rodríguez García', '2025-11-06', '2025-11-04 11:27:10', 0),
(121, 55, 'WhatsApp', 'Mensaje WhatsApp a Cliente 39 López Díaz', '2025-11-07', '2025-11-04 11:27:10', 1),
(122, 56, 'WhatsApp', 'Mensaje WhatsApp a Cliente 40 Moreno Pérez', '2025-11-03', '2025-11-04 11:27:10', 0),
(123, 57, 'WhatsApp', 'Mensaje WhatsApp a Cliente 41 Jiménez Sánchez', '2025-11-08', '2025-11-04 11:27:10', 1),
(124, 58, 'WhatsApp', 'Mensaje WhatsApp a Cliente 42 Ramírez Torres', '2025-11-08', '2025-11-04 11:27:10', 1),
(125, 59, 'WhatsApp', 'Mensaje WhatsApp a Cliente 43 Álvarez Castro', '2025-10-28', '2025-11-04 11:27:10', 1),
(126, 60, 'WhatsApp', 'Mensaje WhatsApp a Cliente 44 Hernández Romero', '2025-11-08', '2025-11-04 11:27:10', 0),
(127, 61, 'WhatsApp', 'Mensaje WhatsApp a Cliente 45 García Domínguez', '2025-11-04', '2025-11-04 11:27:10', 0),
(128, 62, 'WhatsApp', 'Mensaje WhatsApp a Cliente 46 Martínez Navarro', '2025-11-02', '2025-11-04 11:27:10', 0),
(129, 63, 'WhatsApp', 'Mensaje WhatsApp a Cliente 47 Fernández Gómez', '2025-11-07', '2025-11-04 11:27:10', 0),
(130, 64, 'WhatsApp', 'Mensaje WhatsApp a Cliente 48 González Vargas', '2025-11-10', '2025-11-04 11:27:10', 0),
(131, 65, 'WhatsApp', 'Mensaje WhatsApp a Cliente 49 Rodríguez Lara', '2025-11-02', '2025-11-04 11:27:10', 1),
(132, 66, 'WhatsApp', 'Mensaje WhatsApp a Cliente 50 López Silva', '2025-10-30', '2025-11-04 11:27:10', 0),
(133, 67, 'WhatsApp', 'Mensaje WhatsApp a Cliente 51 Moreno Fuentes', '2025-10-30', '2025-11-04 11:27:10', 1),
(134, 68, 'WhatsApp', 'Mensaje WhatsApp a Cliente 52 Jiménez Campos', '2025-11-10', '2025-11-04 11:27:10', 0),
(135, 69, 'WhatsApp', 'Mensaje WhatsApp a Cliente 53 Ramírez Guerrero', '2025-11-09', '2025-11-04 11:27:10', 1),
(136, 70, 'WhatsApp', 'Mensaje WhatsApp a Cliente 54 Álvarez Espinosa', '2025-11-07', '2025-11-04 11:27:10', 0),
(137, 71, 'WhatsApp', 'Mensaje WhatsApp a Cliente 55 Hernández Velasco', '2025-11-04', '2025-11-04 11:27:10', 0),
(138, 72, 'WhatsApp', 'Mensaje WhatsApp a Cliente 56 García Reyes', '2025-11-08', '2025-11-04 11:27:10', 0),
(139, 73, 'WhatsApp', 'Mensaje WhatsApp a Cliente 57 Martínez Ochoa', '2025-11-04', '2025-11-04 11:27:10', 1),
(140, 74, 'WhatsApp', 'Mensaje WhatsApp a Cliente 58 Fernández Burgos', '2025-11-04', '2025-11-04 11:27:10', 1),
(141, 75, 'WhatsApp', 'Mensaje WhatsApp a Cliente 59 González Ramos', '2025-11-04', '2025-11-04 11:27:10', 0),
(142, 76, 'WhatsApp', 'Mensaje WhatsApp a Cliente 60 Rodríguez Costa', '2025-11-02', '2025-11-04 11:27:10', 0),
(144, 14, 'Cita', 'wfwfwf', '2025-11-05', '2025-11-05 15:21:55', 0),
(145, 14, 'Revisión', 'revision', '2025-11-05', '2025-11-05 15:22:24', 0),
(146, 14, 'Cita', 'Toca trabajar', '2025-11-05', '2025-11-05 15:26:39', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre_apellidos` varchar(150) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `convergente` text DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultima_visita` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre_apellidos`, `dni`, `telefono`, `convergente`, `usuario_id`, `fecha_creacion`, `ultima_visita`) VALUES
(1, 'Juan García Pérez', '12345678A', '600123456', 'Fibra 600Mb + 2 líneas móviles 50GB', 1, '2025-10-26 19:43:01', NULL),
(2, 'María López Sánchez', '23456789B', '611234567', 'Fibra 1Gb + 3 líneas móviles ilimitadas', 8778, '2025-10-26 19:43:01', '2025-11-05 10:36:28'),
(3, 'Carlos Martínez Ruiz', '34567890C', '622345678', 'Fibra 300Mb + 1 línea móvil 30GB', 8779, '2025-10-26 19:43:01', NULL),
(4, 'Ana Fernández Torres', '45678901D', '633456789', 'Fibra 600Mb + 4 líneas móviles 100GB', 8780, '2025-10-26 19:43:01', NULL),
(5, 'Pedro Rodríguez Gómez', '56789012E', '644567890', 'Fibra 1Gb + 2 líneas móviles 80GB', 8781, '2025-10-26 19:43:01', NULL),
(6, 'Laura Jiménez Moreno', '67890123F', '655678901', 'Fibra 300Mb + 1 línea móvil 50GB', 8782, '2025-10-26 19:43:01', NULL),
(7, 'Francisco Navarro Díaz', '78901234G', '666789012', 'Fibra 600Mb + 2 líneas móviles 60GB', 8778, '2025-10-26 19:43:01', NULL),
(8, 'Isabel Romero Castro', '89012345H', '677890123', 'Fibra 1Gb + 5 líneas móviles ilimitadas', 8779, '2025-10-26 19:43:01', NULL),
(9, 'Miguel Ángel Herrera Vega', '90123456I', '688901234', 'Fibra 300Mb + 2 líneas móviles 40GB', 8780, '2025-10-26 19:43:01', NULL),
(10, 'Carmen Molina Serrano', '01234567J', '699012345', 'Fibra 600Mb + 3 líneas móviles 70GB', 1, '2025-10-26 19:43:01', NULL),
(11, 'Joselu Solvam', '49601120X', '666666666', '0', 8778, '2025-10-28 15:42:43', '2025-11-05 10:45:38'),
(12, 'Jorge Monzonís', '22222222T', '666666666', '0', 8779, '2025-10-28 16:04:47', '2025-11-05 15:09:39'),
(13, 'Test', '12345678A', '123456789', NULL, 1, '2025-10-29 08:15:19', NULL),
(14, 'David Guijarro Cano', '49601120X', '628086491', '0', 8778, '2025-10-29 08:59:41', '2025-11-05 15:26:39'),
(16, 'Francisco Franco Parera', '48579584X', '666111444', 'Fibra 500 + 2L 50GB', 8778, '2025-11-03 08:50:01', '2025-11-06 16:55:28'),
(17, 'Cliente 1 García López', '12345601A', '600000001', 'Movistar', 8779, '2025-10-05 10:27:10', '2025-10-20 10:27:10'),
(18, 'Cliente 2 Martínez Ruiz', '12345602B', '600000002', 'Vodafone', 8779, '2025-10-06 10:27:10', '2025-10-21 10:27:10'),
(19, 'Cliente 3 Fernández Díaz', '12345603C', '600000003', 'Orange', 8779, '2025-10-07 10:27:10', '2025-10-22 10:27:10'),
(20, 'Cliente 4 González Pérez', '12345604D', '600000004', 'Telefónica', 8779, '2025-10-08 10:27:10', '2025-10-23 10:27:10'),
(21, 'Cliente 5 Rodríguez Sánchez', '12345605E', '600000005', 'Movistar', 8779, '2025-10-09 10:27:10', '2025-10-24 10:27:10'),
(22, 'Cliente 6 López Torres', '12345606F', '600000006', 'Vodafone', 8779, '2025-10-10 10:27:10', '2025-10-25 10:27:10'),
(23, 'Cliente 7 Moreno Castro', '12345607G', '600000007', 'Orange', 8779, '2025-10-11 10:27:10', '2025-10-26 11:27:10'),
(24, 'Cliente 8 Jiménez Romero', '12345608H', '600000008', 'Telefónica', 8779, '2025-10-12 10:27:10', '2025-10-27 11:27:10'),
(25, 'Cliente 9 Ramírez Domínguez', '12345609I', '600000009', 'Movistar', 8779, '2025-10-13 10:27:10', '2025-10-28 11:27:10'),
(26, 'Cliente 10 Álvarez Gómez', '12345610J', '600000010', 'Vodafone', 8779, '2025-10-14 10:27:10', '2025-10-29 11:27:10'),
(27, 'Cliente 11 Hernández Navarro', '12345611K', '600000011', 'Orange', 8779, '2025-10-15 10:27:10', '2025-10-30 11:27:10'),
(28, 'Cliente 12 García Vargas', '12345612L', '600000012', 'Telefónica', 8779, '2025-10-16 10:27:10', '2025-10-31 11:27:10'),
(29, 'Cliente 13 Martínez Lara', '12345613M', '600000013', 'Movistar', 8779, '2025-10-17 10:27:10', '2025-11-01 11:27:10'),
(30, 'Cliente 14 Fernández Silva', '12345614N', '600000014', 'Vodafone', 8779, '2025-10-18 10:27:10', '2025-11-02 11:27:10'),
(31, 'Cliente 15 González Fuentes', '12345615O', '600000015', 'Orange', 8779, '2025-10-19 10:27:10', '2025-11-03 11:27:10'),
(32, 'Cliente 16 Rodríguez Campos', '12345616P', '600000016', 'Telefónica', 8779, '2025-10-20 10:27:10', '2025-11-04 11:27:10'),
(33, 'Cliente 17 López Guerrero', '12345617Q', '600000017', 'Movistar', 8779, '2025-10-21 10:27:10', '2025-11-05 15:11:09'),
(34, 'Cliente 18 Moreno Espinosa', '12345618R', '600000018', 'Vodafone', 8779, '2025-10-22 10:27:10', '2025-10-21 10:27:10'),
(35, 'Cliente 19 Jiménez Velasco', '12345619S', '600000019', 'Orange', 8779, '2025-10-23 10:27:10', '2025-10-22 10:27:10'),
(36, 'Cliente 20 Ramírez Reyes', '12345620T', '600000020', 'Telefónica', 8779, '2025-10-24 10:27:10', '2025-10-23 10:27:10'),
(37, 'Cliente 21 Álvarez Ochoa', '12345621U', '600000021', 'Movistar', 8779, '2025-10-25 10:27:10', '2025-10-24 10:27:10'),
(38, 'Cliente 22 Hernández Burgos', '12345622V', '600000022', 'Vodafone', 8779, '2025-10-26 11:27:10', '2025-10-25 10:27:10'),
(39, 'Cliente 23 García Ramos', '12345623W', '600000023', 'Orange', 8779, '2025-10-27 11:27:10', '2025-10-26 11:27:10'),
(40, 'Cliente 24 Martínez Costa', '12345624X', '600000024', 'Telefónica', 8779, '2025-10-28 11:27:10', '2025-11-04 11:39:18'),
(41, 'Cliente 25 Fernández Molina', '12345625Y', '600000025', 'Movistar', 8779, '2025-10-29 11:27:10', '2025-10-28 11:27:10'),
(42, 'Cliente 26 González Duque', '12345626Z', '600000026', 'Vodafone', 8779, '2025-10-30 11:27:10', '2025-11-04 17:07:08'),
(43, 'Cliente 27 Rodríguez Cortés', '12345627AA', '600000027', 'Orange', 8779, '2025-10-31 11:27:10', '2025-10-30 11:27:10'),
(44, 'Cliente 28 López Saldaña', '12345628AB', '600000028', 'Telefónica', 8779, '2025-11-01 11:27:10', '2025-10-31 11:27:10'),
(45, 'Cliente 29 Moreno Santana', '12345629AC', '600000029', 'Movistar', 8779, '2025-11-02 11:27:10', '2025-11-01 11:27:10'),
(46, 'Cliente 30 Jiménez Nolasco', '12345630AD', '600000030', 'Vodafone', 8779, '2025-11-03 11:27:10', '2025-11-06 07:09:01'),
(47, 'Cliente 31 Ramírez Quintero', '12345631AE', '600000031', 'Orange', 8779, '2025-11-04 11:27:10', '2025-11-07 12:11:38'),
(48, 'Cliente 32 Álvarez Pinto', '12345632AF', '600000032', 'Telefónica', 8779, '2025-10-05 10:27:10', NULL),
(49, 'Cliente 33 Hernández Mena', '12345633AG', '600000033', 'Movistar', 8779, '2025-10-10 10:27:10', NULL),
(50, 'Cliente 34 García Cano', '12345634AH', '600000034', 'Vodafone', 8779, '2025-10-15 10:27:10', NULL),
(51, 'Cliente 35 Martínez Gómez', '12345635AI', '600000035', 'Orange', 8779, '2025-10-20 10:27:10', NULL),
(52, 'Cliente 36 Fernández Ruiz', '12345636AJ', '600000036', 'Telefónica', 8779, '2025-10-25 10:27:10', NULL),
(53, 'Cliente 37 González López', '12345637AK', '600000037', 'Movistar', 8779, '2025-10-30 11:27:10', '2025-11-04 11:39:38'),
(54, 'Cliente 38 Rodríguez García', '12345638AL', '600000038', 'Vodafone', 8779, '2025-10-31 11:27:10', '2025-10-15 10:27:10'),
(55, 'Cliente 39 López Díaz', '12345639AM', '600000039', 'Orange', 8779, '2025-11-01 11:27:10', '2025-10-16 10:27:10'),
(56, 'Cliente 40 Moreno Pérez', '12345640AN', '600000040', 'Telefónica', 8779, '2025-11-02 11:27:10', '2025-10-17 10:27:10'),
(57, 'Cliente 41 Jiménez Sánchez', '12345641AO', '600000041', 'Movistar', 8779, '2025-11-03 11:27:10', '2025-11-06 07:09:03'),
(58, 'Cliente 42 Ramírez Torres', '12345642AP', '600000042', 'Vodafone', 8779, '2025-11-04 11:27:10', '2025-11-07 09:33:23'),
(59, 'Cliente 43 Álvarez Castro', '12345643AQ', '600000043', 'Orange', 8779, '2025-10-05 10:27:10', '2025-10-10 10:27:10'),
(60, 'Cliente 44 Hernández Romero', '12345644AR', '600000044', 'Telefónica', 8779, '2025-10-07 10:27:10', '2025-10-11 10:27:10'),
(61, 'Cliente 45 García Domínguez', '12345645AS', '600000045', 'Movistar', 8779, '2025-10-09 10:27:10', '2025-10-12 10:27:10'),
(62, 'Cliente 46 Martínez Navarro', '12345646AT', '600000046', 'Vodafone', 8779, '2025-10-11 10:27:10', '2025-10-13 10:27:10'),
(63, 'Cliente 47 Fernández Gómez', '12345647AU', '600000047', 'Orange', 8779, '2025-10-13 10:27:10', '2025-10-14 10:27:10'),
(64, 'Cliente 48 González Vargas', '12345648AV', '600000048', 'Telefónica', 8779, '2025-10-15 10:27:10', '2025-10-15 10:27:10'),
(65, 'Cliente 49 Rodríguez Lara', '12345649AW', '600000049', 'Movistar', 8779, '2025-10-17 10:27:10', '2025-10-16 10:27:10'),
(66, 'Cliente 50 López Silva', '12345650AX', '600000050', 'Vodafone', 8779, '2025-10-19 10:27:10', '2025-10-17 10:27:10'),
(67, 'Cliente 51 Moreno Fuentes', '12345651AY', '600000051', 'Orange', 8779, '2025-10-21 10:27:10', '2025-10-18 10:27:10'),
(68, 'Cliente 52 Jiménez Campos', '12345652AZ', '600000052', 'Telefónica', 8779, '2025-10-23 10:27:10', '2025-10-19 10:27:10'),
(69, 'Cliente 53 Ramírez Guerrero', '12345653BA', '600000053', 'Movistar', 8779, '2025-10-25 10:27:10', '2025-10-20 10:27:10'),
(70, 'Cliente 54 Álvarez Espinosa', '12345654BB', '600000054', 'Vodafone', 8779, '2025-10-27 11:27:10', '2025-10-21 10:27:10'),
(71, 'Cliente 55 Hernández Velasco', '12345655BC', '600000055', 'Orange', 8779, '2025-10-29 11:27:10', '2025-10-22 10:27:10'),
(72, 'Cliente 56 García Reyes', '12345656BD', '600000056', 'Telefónica', 8779, '2025-10-31 11:27:10', '2025-10-23 10:27:10'),
(73, 'Cliente 57 Martínez Ochoa', '12345657BE', '600000057', 'Movistar', 8779, '2025-11-02 11:27:10', '2025-10-24 10:27:10'),
(74, 'Cliente 58 Fernández Burgos', '12345658BF', '600000058', 'Vodafone', 8779, '2025-11-04 11:27:10', '2025-11-06 07:08:58'),
(75, 'Cliente 59 González Ramos', '12345659BG', '600000059', 'Orange', 8779, '2025-11-03 11:27:10', '2025-11-05 12:10:36'),
(76, 'Cliente 60 Rodríguez Costa', '12345660BH', '600000060', 'Telefónica', 8779, '2025-11-01 11:27:10', '2025-10-27 11:27:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_etiqueta`
--

CREATE TABLE `cliente_etiqueta` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `etiqueta_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `cliente_etiqueta`
--

INSERT INTO `cliente_etiqueta` (`id`, `cliente_id`, `etiqueta_id`) VALUES
(7, 14, 2),
(8, 14, 1),
(9, 14, 5),
(10, 11, 16),
(11, 16, 3),
(12, 16, 7),
(13, 12, 2),
(14, 12, 6);

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
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `texto` text NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `notas`
--

INSERT INTO `notas` (`id`, `cliente_id`, `texto`, `fecha_creacion`) VALUES
(1, 14, 'a', '2025-10-29 08:59:41'),
(3, 16, 'Cliente exigente y un poco radical.', '2025-11-03 08:50:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `contraseña` varchar(100) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `rol` enum('encargado','tienda') NOT NULL DEFAULT 'tienda',
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `contraseña`, `fecha_creacion`, `rol`, `foto`) VALUES
(1, 'Phone House Admin', '$2y$10$KmLMmvJZYT7IeWKVjHcdJeb1bn7AKd017zqKQm1GfHaLWcYKE7ZZq', '2025-10-26 19:21:32', 'encargado', 'images/profiles/admin_1.png'),
(8778, 'Phone House Quart', '$2y$10$a1j0U9KWWNjlI7NTe2d43eezGzgBxXqfxfZy9VrjUPyxtCkIUGAXG', '2025-10-26 19:21:32', 'tienda', 'images/profiles/tienda_8778.png'),
(8779, 'Phone House Manises', '$2y$10$9nRHwvYBniTgl6KCd/VMB.spBH1TxIUIiWOG4apJ2s6cpGNjUhTa6', '2025-10-26 19:21:32', 'tienda', 'images/profiles/tienda_8779.png'),
(8780, 'Phone House Paterna', '$2y$10$xGtbb3/CyCex6pBnKXXUKu1W5Ie5HaKVCgjbZjsJscgFUmW0ig1Ri', '2025-10-26 19:21:32', 'tienda', 'images/profiles/tienda_8780.png'),
(8781, 'Phone House Mislata', '$2y$10$UstE1EOnQ1L4Z3txyulhEOYxZH8XP6YNGgVH5hKiouRnwOfvz8UAu', '2025-10-26 19:21:32', 'tienda', 'images/profiles/tienda_8781.png'),
(8782, 'Phone House Torrent', '$2y$10$Ah0/JlOqFSzFYPI9gCB9WOBzuD.Kb4MSplYwmkiZXwkHj/g0zOWvC', '2025-10-26 19:21:32', 'tienda', 'images/profiles/tienda_8782.png');

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
  ADD KEY `idx_clientes_usuario` (`usuario_id`);

--
-- Indices de la tabla `cliente_etiqueta`
--
ALTER TABLE `cliente_etiqueta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `etiqueta_id` (`etiqueta_id`);

--
-- Indices de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT de la tabla `cliente_etiqueta`
--
ALTER TABLE `cliente_etiqueta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  ADD CONSTRAINT `clientes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `cliente_etiqueta`
--
ALTER TABLE `cliente_etiqueta`
  ADD CONSTRAINT `cliente_etiqueta_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cliente_etiqueta_ibfk_2` FOREIGN KEY (`etiqueta_id`) REFERENCES `etiquetas` (`id`);

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

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
