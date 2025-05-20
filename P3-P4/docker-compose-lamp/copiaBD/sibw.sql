-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: database:3306
-- Tiempo de generación: 20-05-2025 a las 19:35:37
-- Versión del servidor: 8.4.4
-- Versión de PHP: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sibw`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Comentarios`
--

CREATE TABLE `Comentarios` (
  `id` int NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `id_pelicula` int NOT NULL,
  `modificado` tinyint(1) DEFAULT NULL,
  `id_usuario` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Comentarios`
--

INSERT INTO `Comentarios` (`id`, `fecha_hora`, `comentario`, `id_pelicula`, `modificado`, `id_usuario`) VALUES
(20, '2025-05-15 21:46:00', 'guay', 1, 1, 2),
(21, '2025-05-15 22:06:00', 'hola', 1, NULL, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enlaces`
--

CREATE TABLE `enlaces` (
  `tipo` varchar(50) NOT NULL,
  `enlace` text NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `enlaces`
--

INSERT INTO `enlaces` (`tipo`, `enlace`, `nombre`) VALUES
('Cines', 'https://cinemadrigalgranad.wixsite.com/cinemadrigal', 'Cine Madrigal'),
('Otros', 'https://www.filmaffinity.com/es/main.html', 'Filmaffinity'),
('Revistas de cine', 'https://www.fotogramas.es/.html', 'Fotogramas'),
('Otros', 'https://www.imdb.com/es-es/', 'IMDb'),
('Otros', 'https://www.justwatch.com/es', 'JustWatch'),
('Cines', 'https://kinepolis.es/cines/kinepolis-granada/informacion/', 'Kinepolis Granada'),
('Otros', 'https://www.rottentomatoes.com/', 'Rotten Tomatoes'),
('Revistas de cine', 'https://www.sensacine.com/', 'SensaCine');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria`
--

CREATE TABLE `galeria` (
  `id` int NOT NULL,
  `id_pelicula` int NOT NULL,
  `imagen` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `galeria`
--

INSERT INTO `galeria` (`id`, `id_pelicula`, `imagen`) VALUES
(1, 1, '/images/galeria/galeria1.jpg'),
(2, 1, '/images/galeria/galeria2.jpg'),
(3, 1, '/images/galeria/galeria3.jpg'),
(4, 1, '/images/galeria/galeria4.jpg'),
(5, 1, '/images/galeria/galeria5.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `palabrasProhibidas`
--

CREATE TABLE `palabrasProhibidas` (
  `id` int NOT NULL,
  `palabra` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `palabrasProhibidas`
--

INSERT INTO `palabrasProhibidas` (`id`, `palabra`) VALUES
(1, 'idiota'),
(2, 'tonto'),
(3, 'payaso'),
(4, 'feo'),
(5, 'gilipollas'),
(6, 'horrible'),
(7, 'capullo'),
(8, 'fuck');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peliculas`
--

CREATE TABLE `peliculas` (
  `id` int NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `fecha_estreno` date DEFAULT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `director` varchar(100) DEFAULT NULL,
  `actores_principales` text,
  `sinopsis` text,
  `premios` text,
  `imagen` varchar(150) DEFAULT NULL,
  `escena1` varchar(150) DEFAULT NULL,
  `escena2` varchar(150) DEFAULT NULL,
  `texto_escena1` varchar(50) DEFAULT NULL,
  `texto_escena2` varchar(50) DEFAULT NULL,
  `hashtags` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `peliculas`
--

INSERT INTO `peliculas` (`id`, `titulo`, `fecha_estreno`, `genero`, `director`, `actores_principales`, `sinopsis`, `premios`, `imagen`, `escena1`, `escena2`, `texto_escena1`, `texto_escena2`, `hashtags`) VALUES
(1, 'Piratas del Caribe: La maldición de la perla negra', '2003-07-09', 'Aventura, Fantasía, Acción', 'Gore Verbinski', 'Johnny Depp, Orlando Bloom, Keira Knightley, Geoffrey Rush, Jack Davenport', 'En el mar Caribe, en la época colonial, el pirata Jack Sparrow (Johnny Depp) roba el barco del Capitán Barbossa (Geoffrey Rush). Cuando llega a Port Royal, conoce a la bella Elizabeth Swann (Keira Knightley), hija del gobernador y de la que se enamora el herrero Will Turner (Orlando Bloom).\r\n\r\nLos problemas comienzan cuando Barbossa, tras secuestrar a Elizabeth, ataca la ciudad con su barco, la Perla Negra. Elizabeth descubrirá que los piratas están malditos: la luz de la luna los convierte en esqueletos vivientes. Para romper el hechizo necesitan una moneda de oro, parte de un tesoro azteca, y la sangre de quien la encontró. Will le pide ayuda a Jack para rescatar a Elizabeth y él accede con la condición de recuperar su barco.', 'Por su actuación como el capitán Jack Sparrow, Johnny Depp ganó el premio al mejor actor en los Premios del Sindicato de Actores, en los Premios MTV Movie y en los Premios Empire, además de estar nominado para el mismo premio en los Globos de Oro, los Premios BAFTA y en la 76.ª Ceremonia de los Oscar, en la que The Curse of the Black Pearl estuvo nominada al mejor maquillaje, edición de sonido, mejor sonido y mejores efectos visuales. The Curse of the Black Pearl ganó el premio al mejor maquillaje y peluquería en los Premios BAFTA, un Premios Saturn por mejor vestuario, un Golden Reel Award por la mejor edición de sonido, dos VES Award por efectos visuales y el People\'s Choice Awards en la categoría Película Favorita. ', '/images/portadas/piratas.jpg', '/images/escenas/escena1.png', '/images/escenas/escena2.png', 'Jack Sparrow amenazando a Elizabeth Swann ', 'Jack Sparrow y Will Turner luchando', NULL),
(2, 'El Caballero Oscuro', '2008-07-18', 'Acción, Crimen, Drama', 'Christopher Nolan', 'Christian Bale, Heath Ledger, Aaron Eckhart, Michael Caine, Gary Oldman, Morgan Freeman', 'Con la ayuda del teniente Jim Gordon y el fiscal del distrito Harvey Dent, Batman se propone desmantelar las organizaciones criminales que asolan Gotham. La colaboración resulta eficaz, pero pronto se ven atrapados por el caos desatado por un criminal emergente conocido como El Joker, que sumerge la ciudad en la anarquía y obliga a Batman a cuestionar todo lo que representa.', '\"El Caballero Oscuro\" ganó 2 premios Oscar: Mejor Actor de Reparto (Heath Ledger) y Mejor Edición de Sonido. Fue nominada a 8 premios Oscar en total. También recibió múltiples galardones en los BAFTA, Critics’ Choice Awards, Empire Awards y fue reconocida como una de las mejores películas del siglo XXI por numerosos medios especializados.', '/images/portadas/batman.jpg', '/images/escenas/escena1_batman.jpg', '/images/escenas/escena2_batman.jpg', 'Batman y el Joker en un interrogatorio', 'El actor Christian Bale junto al traje de Batman', NULL),
(3, 'Dune', '2021-09-17', 'Ciencia ficción, Aventura, Drama', 'Denis Villeneuve', 'Timothée Chalamet, Rebecca Ferguson, Oscar Isaac, Zendaya, Jason Momoa, Josh Brolin, Stellan Skarsgård', 'En un futuro lejano, Paul Atreides, un joven brillante y talentoso, debe viajar al planeta más peligroso del universo para asegurar el futuro de su familia y su pueblo. Mientras fuerzas malévolas se enfrentan por el control del recurso más valioso existente —la especia melange—, sólo los que puedan conquistar sus miedos sobrevivirán.', 'Ganadora de 6 premios Oscar, incluyendo Mejor Fotografía, Mejor Montaje y Mejores Efectos Visuales. También fue nominada a Mejor Película y Mejor Dirección. Ganó múltiples BAFTA, Critics’ Choice y premios técnicos por su impresionante diseño sonoro y visual.', '/images/portadas/dune.jpg', '/images/escenas/escena1_dune.jpg', '/images/escenas/escena2_dune.jpg', 'Paul Atreides junto a su madre', 'Gusano de Arena en Arrakis', 'accion,drama'),
(4, 'Gladiator', '2000-05-05', 'Acción, Drama, Histórico', 'Ridley Scott', 'Russell Crowe, Joaquin Phoenix, Connie Nielsen, Oliver Reed, Richard Harris, Djimon Hounsou', 'Máximo Décimo Meridio, un poderoso general romano traicionado y reducido a esclavitud, se convierte en gladiador y lucha en la arena del Coliseo en busca de justicia por el asesinato de su familia y del emperador Marco Aurelio. Su camino lo lleva a enfrentarse al corrupto emperador Cómodo en un duelo por el alma de Roma.', 'Ganadora de 5 premios Oscar, incluyendo Mejor Película, Mejor Actor (Russell Crowe) y Mejor Diseño de Vestuario. También obtuvo premios BAFTA y Globos de Oro, convirtiéndose en una de las películas épicas más reconocidas de la historia reciente.', '/images/portadas/gladiator.jpg', '/images/escenas/escena1_gladiator.jpeg', '/images/escenas/escena2_gladiator.jpg', 'Russell Crowe interpretando a Máximo', 'Máximo Décimo Meridio luchando', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `rol` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`rol`) VALUES
('gestor'),
('moderador'),
('registrado'),
('superusuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `email` varchar(50) NOT NULL,
  `rol` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `password`, `email`, `rol`) VALUES
(2, 'Javier Mora', '$2y$10$aog02LrqNHcfrc8ZcL2c6ee1Gnmxt6UYo6PDAhsQBDrHBMfw0Pq2.', 'javier@go.es', 'superusuario'),
(3, 'paco', '$2y$10$xjiGi7h3.Y2aCSIiab6ET.Y3/WQHez.X1sBcfF5IJeOE7m72jWdnW', 'paco@as.es', 'moderador'),
(4, 'laura', '$2y$10$8X1bPiRhEpTBdVZAVQF5m.C7qR5HxkdSjTwiEJZ.4SUBJckkg.WVS', 'adsf@sdf.es', 'registrado'),
(5, 'jose', '$2y$10$kZThj6ea9BI9lg57G340GetJ5Yrfgum.oYzmy19xcWMY9u1WRFUsO', 'jjj@es.es', 'moderador');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Comentarios`
--
ALTER TABLE `Comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comentario_pelicula` (`id_pelicula`),
  ADD KEY `ffk_id_usuario` (`id_usuario`);

--
-- Indices de la tabla `enlaces`
--
ALTER TABLE `enlaces`
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `galeria`
--
ALTER TABLE `galeria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pelicula` (`id_pelicula`);

--
-- Indices de la tabla `palabrasProhibidas`
--
ALTER TABLE `palabrasProhibidas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `peliculas`
--
ALTER TABLE `peliculas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`rol`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rol_usuario` (`rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Comentarios`
--
ALTER TABLE `Comentarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `galeria`
--
ALTER TABLE `galeria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `palabrasProhibidas`
--
ALTER TABLE `palabrasProhibidas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `peliculas`
--
ALTER TABLE `peliculas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Comentarios`
--
ALTER TABLE `Comentarios`
  ADD CONSTRAINT `ffk_id_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comentario_pelicula` FOREIGN KEY (`id_pelicula`) REFERENCES `peliculas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `galeria`
--
ALTER TABLE `galeria`
  ADD CONSTRAINT `galeria_ibfk_1` FOREIGN KEY (`id_pelicula`) REFERENCES `peliculas` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_rol_usuario` FOREIGN KEY (`rol`) REFERENCES `roles` (`rol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
