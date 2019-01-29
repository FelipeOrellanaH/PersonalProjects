-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-01-2019 a las 17:57:56
-- Versión del servidor: 10.1.37-MariaDB
-- Versión de PHP: 7.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `nombre_bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accion_crear_lista`
--

CREATE TABLE `accion_crear_lista` (
  `id` varchar(300) NOT NULL,
  `tipo` varchar(300) NOT NULL,
  `fecha` varchar(300) NOT NULL,
  `hora` varchar(300) NOT NULL,
  `idTablero` varchar(300) NOT NULL,
  `idLista` varchar(300) NOT NULL,
  `nombreLista` varchar(300) NOT NULL,
  `nombreAutor` varchar(300) NOT NULL,
  `idAutor` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accion_crear_tarjeta`
--

CREATE TABLE `accion_crear_tarjeta` (
  `id` varchar(300) NOT NULL,
  `tipo` varchar(300) NOT NULL,
  `fecha` varchar(300) NOT NULL,
  `hora` varchar(300) NOT NULL,
  `idTablero` varchar(300) NOT NULL,
  `idLista` varchar(300) NOT NULL,
  `idTarjeta` varchar(300) NOT NULL,
  `nombreAutor` varchar(300) NOT NULL,
  `idAutor` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accion_extras`
--

CREATE TABLE `accion_extras` (
  `id` varchar(300) NOT NULL,
  `tipo` varchar(300) NOT NULL,
  `fecha` varchar(300) NOT NULL,
  `hora` varchar(300) NOT NULL,
  `idTablero` varchar(300) NOT NULL,
  `nombreAutor` varchar(300) NOT NULL,
  `idAutor` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accion_mover_tarjeta_de_lista`
--

CREATE TABLE `accion_mover_tarjeta_de_lista` (
  `id` varchar(300) NOT NULL,
  `tipo` varchar(300) NOT NULL,
  `fecha` varchar(300) NOT NULL,
  `hora` varchar(300) NOT NULL,
  `idTarjeta` varchar(300) NOT NULL,
  `idTablero` varchar(300) NOT NULL,
  `listaOrigen` varchar(300) NOT NULL,
  `listaDestino` varchar(300) NOT NULL,
  `nombreAutor` varchar(300) NOT NULL,
  `idAutor` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accion_registros_archivado`
--

CREATE TABLE `accion_registros_archivado` (
  `id` varchar(300) NOT NULL,
  `tipo` varchar(300) NOT NULL,
  `fecha` varchar(300) NOT NULL,
  `hora` varchar(300) NOT NULL,
  `idTarjeta` varchar(300) NOT NULL,
  `idLista` varchar(300) NOT NULL,
  `idTablero` varchar(300) NOT NULL,
  `estado` varchar(300) NOT NULL,
  `nombreAutor` varchar(300) NOT NULL,
  `idAutor` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista`
--

CREATE TABLE `lista` (
  `nombre` varchar(300) NOT NULL,
  `id` varchar(300) NOT NULL,
  `idTablero` varchar(300) NOT NULL,
  `posicion` varchar(300) NOT NULL,
  `archivado` int(11) NOT NULL,
  `fechaArchivado` varchar(300) NOT NULL,
  `horaArchivado` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membresia`
--

CREATE TABLE `membresia` (
  `idMembresia` varchar(300) NOT NULL,
  `idMiembro` varchar(300) NOT NULL,
  `tipoMiembro` varchar(300) NOT NULL,
  `idTablero` varchar(300) NOT NULL,
  `nombreTablero` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tablero`
--

CREATE TABLE `tablero` (
  `nombre` varchar(300) NOT NULL,
  `id` varchar(300) NOT NULL,
  `shortLink` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarjeta`
--

CREATE TABLE `tarjeta` (
  `nombre` varchar(300) NOT NULL,
  `id` varchar(300) NOT NULL,
  `idTablero` varchar(300) NOT NULL,
  `idLista` varchar(300) NOT NULL,
  `posicionEnLista` varchar(300) NOT NULL,
  `shortLink` varchar(300) NOT NULL,
  `archivado` int(11) NOT NULL,
  `fechaArchivado` varchar(300) NOT NULL,
  `horaArchivado` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accion_crear_lista`
--
ALTER TABLE `accion_crear_lista`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `accion_crear_tarjeta`
--
ALTER TABLE `accion_crear_tarjeta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `accion_extras`
--
ALTER TABLE `accion_extras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idTablero` (`idTablero`);

--
-- Indices de la tabla `accion_mover_tarjeta_de_lista`
--
ALTER TABLE `accion_mover_tarjeta_de_lista`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idTablero` (`idTablero`);

--
-- Indices de la tabla `accion_registros_archivado`
--
ALTER TABLE `accion_registros_archivado`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idTablero` (`idTablero`);

--
-- Indices de la tabla `lista`
--
ALTER TABLE `lista`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idTablero` (`idTablero`);

--
-- Indices de la tabla `membresia`
--
ALTER TABLE `membresia`
  ADD PRIMARY KEY (`idMembresia`),
  ADD KEY `idTablero` (`idTablero`);

--
-- Indices de la tabla `tablero`
--
ALTER TABLE `tablero`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tarjeta`
--
ALTER TABLE `tarjeta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idTablero` (`idTablero`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accion_crear_tarjeta`
--
ALTER TABLE `accion_crear_tarjeta`
  ADD CONSTRAINT `accion_crear_tarjeta_ibfk_1` FOREIGN KEY (`idTarjeta`) REFERENCES `tarjeta` (`id`);

--
-- Filtros para la tabla `accion_extras`
--
ALTER TABLE `accion_extras`
  ADD CONSTRAINT `accion_extras_ibfk_1` FOREIGN KEY (`idTablero`) REFERENCES `tablero` (`id`);

--
-- Filtros para la tabla `accion_mover_tarjeta_de_lista`
--
ALTER TABLE `accion_mover_tarjeta_de_lista`
  ADD CONSTRAINT `accion_mover_tarjeta_de_lista_ibfk_1` FOREIGN KEY (`idTablero`) REFERENCES `tablero` (`id`);

--
-- Filtros para la tabla `accion_registros_archivado`
--
ALTER TABLE `accion_registros_archivado`
  ADD CONSTRAINT `accion_registros_archivado_ibfk_1` FOREIGN KEY (`idTablero`) REFERENCES `tablero` (`id`);

--
-- Filtros para la tabla `lista`
--
ALTER TABLE `lista`
  ADD CONSTRAINT `lista_ibfk_1` FOREIGN KEY (`idTablero`) REFERENCES `tablero` (`id`);

--
-- Filtros para la tabla `membresia`
--
ALTER TABLE `membresia`
  ADD CONSTRAINT `membresia_ibfk_1` FOREIGN KEY (`idTablero`) REFERENCES `tablero` (`id`);

--
-- Filtros para la tabla `tarjeta`
--
ALTER TABLE `tarjeta`
  ADD CONSTRAINT `tarjeta_ibfk_1` FOREIGN KEY (`idTablero`) REFERENCES `tablero` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
