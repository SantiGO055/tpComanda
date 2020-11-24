-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 24-11-2020 a las 21:49:40
-- Versión del servidor: 10.3.16-MariaDB
-- Versión de PHP: 7.3.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `id15458056_tpcomanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `updated_at`, `created_at`, `nombre`, `apellido`, `usuario`, `clave`) VALUES
(19, '2020-11-24 15:23:47', '2020-11-24 15:23:47', 'lionel', 'messi', 'LioMesi', '$2y$10$CHu36rLw1pdf7bppUHLz1OAHlPX50T58nUdDvqy.d/ldNe2gmoVda'),
(20, '2020-11-24 17:06:28', '2020-11-24 17:06:28', 'lionel', 'messi', 'LioMesi', '$2y$10$QhUCbrC99yKQZvKhLv8wIOh1P.Ww2.xEBC.hv6gdpShTqbNozblqu');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo` varchar(15) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `disponible` tinyint(1) NOT NULL,
  `id_sector` int(11) NOT NULL,
  `operaciones` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `updated_at`, `created_at`, `tipo`, `nombre`, `apellido`, `disponible`, `id_sector`, `operaciones`, `id_estado`) VALUES
(27, '2020-11-24 15:26:36', '2020-11-23 21:11:39', 'bartender', 'asdqwe', 'coca11a', 1, 1, 11, 1),
(28, '2020-11-24 15:26:36', '2020-11-23 21:11:46', 'cervecero', 'zxcxcb', 'qwe', 1, 2, 11, 1),
(29, '2020-11-24 15:26:36', '2020-11-23 21:12:04', 'cocinero', 'uio', 'uiouio', 1, 3, 11, 1),
(30, '2020-11-24 15:25:29', '2020-11-23 22:09:38', 'mozo', 'jkljkl', 'jkljkl', 0, 4, 5, 1),
(31, '2020-11-24 17:06:33', '2020-11-24 01:30:19', 'mozo', 'sergioasd', 'coca123', 0, 4, 5, 1),
(32, '2020-11-24 17:06:50', '2020-11-24 17:06:23', 'mozo', 'jorge', 'coca123', 1, 4, 1, 1),
(33, '2020-11-24 17:09:06', '2020-11-24 17:09:01', 'mozo', 'jorge', 'coca', 1, 4, 1, 1),
(34, '2020-11-24 17:09:52', '2020-11-24 17:09:50', 'mozo', 'jorge', 'coc', 1, 4, 1, 1),
(35, '2020-11-24 17:10:21', '2020-11-24 17:10:17', 'mozo', 'jorge', 'co', 1, 4, 1, 1),
(36, '2020-11-24 17:13:30', '2020-11-24 17:13:27', 'mozo', 'jorge', 'c', 1, 4, 1, 1),
(37, '2020-11-24 17:13:49', '2020-11-24 17:13:35', 'mozo', 'jorge', 'c4', 1, 4, 1, 1),
(38, '2020-11-24 17:15:16', '2020-11-24 17:15:14', 'mozo', 'jorge', 'c41', 0, 4, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_empleados`
--

CREATE TABLE `estado_empleados` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estado_empleados`
--

INSERT INTO `estado_empleados` (`id`, `descripcion`) VALUES
(1, 'ACTIVO'),
(2, 'SUSPENDIDO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_mesa`
--

CREATE TABLE `estado_mesa` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estado_mesa`
--

INSERT INTO `estado_mesa` (`id`, `descripcion`) VALUES
(1, 'con cliente esperando pedido'),
(2, 'con clientes comiendo'),
(3, 'con clientes pagando'),
(4, 'cerrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_pedido`
--

CREATE TABLE `estado_pedido` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estado_pedido`
--

INSERT INTO `estado_pedido` (`id`, `descripcion`) VALUES
(1, 'PENDIENTE'),
(2, 'EN PREPARACION'),
(3, 'LISTO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tiempoEstimado` time NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `codigo` varchar(5) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `descripcion`, `codigo`, `id_estado`, `created_at`, `updated_at`) VALUES
(1, '', '58940', 3, '2020-11-23 02:09:54', '2020-11-24 15:27:00'),
(2, '', '13f10', 2, '2020-11-23 02:10:18', '2020-11-24 14:47:31'),
(3, '', '93216', 1, '2020-11-23 02:12:04', '2020-11-24 01:36:35'),
(4, '', '01498', 1, '2020-11-23 02:12:07', '2020-11-24 01:37:31'),
(21, 'mesa para 4 personas', '93021', 4, '2020-11-24 14:13:38', '2020-11-24 14:40:25'),
(22, 'mesa para 4 personas', '31163', 4, '2020-11-24 14:56:27', '2020-11-24 17:06:33'),
(23, 'mesa para 4 personas', '91726', 1, '2020-11-24 14:57:23', '2020-11-24 17:06:50'),
(24, 'mesa para 4 personas', '97086', 1, '2020-11-24 14:57:56', '2020-11-24 17:09:06'),
(25, 'mesa para 8 personas', '64484', 1, '2020-11-24 15:05:30', '2020-11-24 17:09:52'),
(26, 'mesa para 8 personas', '08305', 1, '2020-11-24 15:06:04', '2020-11-24 17:10:21'),
(27, 'mesa para 8 personas', '27355', 4, '2020-11-24 15:53:00', '2020-11-24 17:13:30'),
(28, 'mesa para 8 personas', '20806', 1, '2020-11-24 17:13:46', '2020-11-24 17:13:49'),
(29, 'mesa para 8 personas', '65454', 1, '2020-11-24 17:13:47', '2020-11-24 17:15:16'),
(30, 'mesa para 8 personas', '59869', 4, '2020-11-24 17:13:48', '2020-11-24 17:13:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tiempo` time NOT NULL,
  `precio` decimal(10,0) NOT NULL,
  `codigo` varchar(5) NOT NULL,
  `detalle` varchar(100) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `id_mesa` int(11) NOT NULL,
  `id_sector` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sectores`
--

CREATE TABLE `sectores` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sectores`
--

INSERT INTO `sectores` (`id`, `descripcion`) VALUES
(1, 'BARRA VINOS Y TRAGOS'),
(2, 'BARRA CHOPERA'),
(3, 'COCINA'),
(4, 'CANDY BAR');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socios`
--

CREATE TABLE `socios` (
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `socios`
--

INSERT INTO `socios` (`created_at`, `updated_at`, `id`, `nombre`, `apellido`, `usuario`, `clave`) VALUES
('2020-11-24 15:23:55', '2020-11-24 15:23:55', 18, 'santi', 'perez', 'SantiP', '$2y$10$fRnbKQ5zlmhWQ6FAcEQ9AO629rV3vDA/An7NSLJYPl5xHvq/O2Ooi');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_sector` (`id_sector`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `estado_empleados`
--
ALTER TABLE `estado_empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estado_mesa`
--
ALTER TABLE `estado_mesa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estado_pedido`
--
ALTER TABLE `estado_pedido`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_empleado` (`id_empleado`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_estado` (`id_estado`),
  ADD KEY `id_empleado` (`id_empleado`),
  ADD KEY `id_mesa` (`id_mesa`);

--
-- Indices de la tabla `sectores`
--
ALTER TABLE `sectores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `socios`
--
ALTER TABLE `socios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `estado_empleados`
--
ALTER TABLE `estado_empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT de la tabla `socios`
--
ALTER TABLE `socios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sectores` (`id`),
  ADD CONSTRAINT `empleados_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado_empleados` (`id`);

--
-- Filtros para la tabla `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`);

--
-- Filtros para la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD CONSTRAINT `mesas_ibfk_1` FOREIGN KEY (`id_estado`) REFERENCES `estado_mesa` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado_pedido` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_4` FOREIGN KEY (`id_mesa`) REFERENCES `mesas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
