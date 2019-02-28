-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-02-2019 a las 12:32:57
-- Versión del servidor: 5.6.43
-- Versión de PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accion_crear_lista`
--

CREATE TABLE `accion_crear_lista` (
  `id` varchar(300),
  `tipo` varchar(300) ,
  `fecha` varchar(300) ,
  `hora` varchar(300) ,
  `idTablero` varchar(300) ,
  `idLista` varchar(300) ,
  `nombreLista` varchar(300),
  `nombreAutor` varchar(300) ,
  `idAutor` varchar(300)
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
-- Estructura de tabla para la tabla `accion_mover_tarjeta_de_tablero`
--

CREATE TABLE `accion_mover_tarjeta_de_tablero` (
  `id` varchar(300) NOT NULL,
  `tipo` varchar(300) NOT NULL,
  `fecha` varchar(300) NOT NULL,
  `hora` varchar(300) NOT NULL,
  `idTarjeta` varchar(300) NOT NULL,
  `tableroOrigen` varchar(300) NOT NULL,
  `tableroDestino` varchar(300) NOT NULL,
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
  `idTarjeta` varchar(300) DEFAULT NULL,
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
  `shortLink` varchar(300) NOT NULL,
  `closed` int(30) NOT NULL
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
  `horaArchivado` varchar(300) NOT NULL,
  `dia_ultima_actividad` varchar(300) NOT NULL,
  `hora_ultima_actividad` varchar(300) NOT NULL,
  `dia_expiracion` varchar(300) DEFAULT NULL,
  `hora_expiracion` varchar(300) DEFAULT NULL,
  `expiracion_Completada` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `accion_mover_tarjeta_de_lista`
--
ALTER TABLE `accion_mover_tarjeta_de_lista`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `accion_mover_tarjeta_de_tablero`
  ADD PRIMARY KEY (`id`);


--
-- Indices de la tabla `accion_registros_archivado`
--
ALTER TABLE `accion_registros_archivado`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `lista`
--
ALTER TABLE `lista`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `membresia`
--
ALTER TABLE `membresia`
  ADD PRIMARY KEY (`idMembresia`);

--
-- Indices de la tabla `tablero`
--
ALTER TABLE `tablero`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tarjeta`
--
ALTER TABLE `tarjeta`
  ADD PRIMARY KEY (`id`),;


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

