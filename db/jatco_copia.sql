-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-02-2025 a las 16:06:50
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
-- Base de datos: `jatco_copia`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`prueba_beta`@`%` PROCEDURE `buscar_usuario` (IN `p_filtro_nombre` VARCHAR(255), IN `p_filtro_correo` VARCHAR(255), IN `p_filtro_usuario` VARCHAR(50))   BEGIN
    SELECT 
        nombre, 
        correo, 
        usuario
    FROM 
        configuracion_usuario
    WHERE 
        (nombre LIKE CONCAT('%', p_filtro_nombre, '%') OR p_filtro_nombre IS NULL)
        AND (correo LIKE CONCAT('%', p_filtro_correo, '%') OR p_filtro_correo IS NULL)
        AND (usuario LIKE CONCAT('%', p_filtro_usuario, '%') OR p_filtro_usuario IS NULL);
END$$

CREATE DEFINER=`prueba_beta`@`%` PROCEDURE `editar_usuario` (IN `p_id_usuario` INT, IN `p_nombre` VARCHAR(250), IN `p_correo` VARCHAR(100), IN `p_usuario` VARCHAR(100), IN `p_pwd` VARCHAR(100), IN `p_rol` INT)   BEGIN
    -- Actualizar datos del cliente en la tabla cliente_datos
    UPDATE configuracion_usuario
    SET 
		nombre = p_nombre,
        correo = p_correo,
        usuario = p_usuario,
        pwd = p_pwd,
        id_estado = 9,
        id_rol = p_rol
    WHERE id_usuario = p_id_usuario;
END$$

CREATE DEFINER=`prueba_beta`@`%` PROCEDURE `eliminar_usuario` (IN `p_id_usuario` INT)   BEGIN   
    -- Then, delete the user from configuracion_usuario
    DELETE FROM configuracion_usuario WHERE id_usuario = p_id_usuario;
END$$

CREATE DEFINER=`prueba_beta`@`%` PROCEDURE `iniciar_sesion` (IN `p_usuario` VARCHAR(50), IN `p_pwd` VARCHAR(50))   BEGIN
    DECLARE v_usuario_existe INT DEFAULT 0;
    DECLARE v_id_usuario INT;
    DECLARE v_nombre_usuario VARCHAR(50);
    DECLARE v_correo VARCHAR(100);
    DECLARE v_usuario VARCHAR(50);
    DECLARE v_pwd VARCHAR(50);
    DECLARE v_id_estado INT;
    DECLARE v_id_estado_descripcion VARCHAR(100);
    DECLARE v_id_rol INT;
    DECLARE v_id_rol_descripcion VARCHAR(100);
    DECLARE v_id_area INT;
    DECLARE v_id_area_descripcion VARCHAR(100);

    -- Comprobamos si el usuario existe
    SELECT COUNT(*) 
    INTO v_usuario_existe
    FROM configuracion_usuario 
    WHERE usuario = p_usuario 
        AND pwd = p_pwd;

    -- Si las credenciales son correctas
    IF v_usuario_existe > 0 THEN
        -- Obtenemos los datos del usuario
        SELECT 
            usu.id_usuario, 
            usu.nombre, 
            usu.correo, 
            usu.usuario, 
            usu.pwd, 
            usu.id_estado, 
            estado.descripcion, 
            usu.id_rol, 
            rol.descripcion, 
            usu.id_area, 
            area.descripcion
        INTO 
            v_id_usuario, 
            v_nombre_usuario, 
            v_correo,
            v_usuario,
            v_pwd,
            v_id_estado, 
            v_id_estado_descripcion,
            v_id_rol, 
            v_id_rol_descripcion,
            v_id_area, 
            v_id_area_descripcion
        FROM 
            configuracion_usuario usu
        LEFT JOIN 
            configuracion_estado estado ON estado.id_estado = usu.id_estado
        LEFT JOIN 
            configuracion_usuario_rol rol ON rol.id_rol = usu.id_rol
        LEFT JOIN 
            configuracion_estado_area area ON area.id_area = usu.id_area
        WHERE 
            usu.usuario = p_usuario 
            AND usu.pwd = p_pwd
        LIMIT 1;

        -- Si el usuario está inactivo (id_estado = 2)
        IF v_id_estado = 2 THEN
            SELECT 'Usuario inactivo' AS mensaje;
        -- Si el usuario tiene un rol y área válidos para el acceso
        ELSEIF v_id_area = 1 AND v_id_rol = 1 THEN
            SELECT 'Inicio de sesión exitoso' AS mensaje,
                   v_id_usuario AS id_usuario,
                   v_nombre_usuario AS nombre,
                   v_id_rol AS id_rol,
                   v_id_rol_descripcion AS rol_descripcion,
                   v_id_estado AS id_estado,
                   v_id_area AS id_area;
        ELSE
            -- Si el área o el rol no son válidos, mostramos el mensaje con la descripción del área
            SELECT CONCAT('Tu usuario es: ', v_id_area_descripcion, ', no puedes ingresar') AS mensaje;
        END IF;
    ELSE
        -- Si las credenciales no coinciden
        SELECT 'Credenciales incorrectas' AS mensaje;
    END IF;
END$$

CREATE DEFINER=`prueba_beta`@`%` PROCEDURE `insertar_configuracion_ingreso` (IN `p_fecha` DATE, IN `p_ip` VARCHAR(50), IN `p_hora_entrada` TIME, IN `p_id_usuario` INT, IN `p_dispositivo` VARCHAR(250))   BEGIN
    -- Insertar datos en la tabla configuracion_ingreso
    DECLARE v_nombre_usuario VARCHAR(250);
    
    SELECT 
		nombre
	INTO
		v_nombre_usuario
    FROM 
		configuracion_usuario 
	WHERE id_usuario = p_id_usuario;
    
    INSERT INTO `configuracion_ingreso` (`fecha`, `ip`, `hora_entrada`, `hora_salida`, `dispositivo`, `usuario`)
    VALUES (p_fecha, p_ip, p_hora_entrada, NULL, p_dispositivo, v_nombre_usuario);
END$$

CREATE DEFINER=`prueba_beta`@`%` PROCEDURE `insertar_usuario` (IN `p_nombre` VARCHAR(255), IN `p_correo` VARCHAR(255), IN `p_usuario` VARCHAR(50), IN `p_pwd` VARCHAR(255), IN `p_id_rol` INT, IN `p_id_area` INT)   BEGIN
    INSERT INTO configuracion_usuario (
        nombre, 
        correo, 
        usuario, 
        pwd, 
        id_estado, 
        id_rol,
        id_area
    )
    VALUES (
        p_nombre, 
        p_correo, 
        p_usuario, 
        p_pwd, 
        1, 
        p_id_rol,
        p_id_area
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `mostrar_configuracion_area` ()   BEGIN
	select * from configuracion_estado_area;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `mostrar_configuracion_usuario_rol` ()   BEGIN
	select * from configuracion_usuario_rol;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `mostrar_ingresos` ()   BEGIN
	select * from configuracion_ingreso;
END$$

CREATE DEFINER=`prueba_beta`@`%` PROCEDURE `mostrar_usuarios` ()   BEGIN
	select 
		usu.id_usuario, 
		usu.nombre, 
		usu.correo, 
		usu.usuario, 
		usu.pwd, 
		usu.id_estado, 
		estado.descripcion as estado,
		usu.id_rol, 
		rol.descripcion as rol,
		usu.id_area,
		area.descripcion as area
	from 
		configuracion_usuario usu
	left join 
		configuracion_usuario_rol rol on rol.id_rol = usu.id_rol
	left join 
		configuracion_estado_area area on area.id_area = usu.id_area
	left join
		configuracion_estado estado on estado.id_estado = usu.id_estado;
END$$

CREATE DEFINER=`prueba_beta`@`%` PROCEDURE `mostrar_usuario_id` (IN `p_id_usuario` INT)   BEGIN
	select 
		usu.id_usuario,
        usu.nombre, 
        usu.correo, 
        usu.usuario, 
        usu.pwd, 
        usu.id_estado,
        estado.descripcion as estado,
        usu.id_rol,
        rol.descripcion as rol,
        usu.id_area,
        area.descripcion as area
	from 
		configuracion_usuario usu
	left join 
		configuracion_usuario_rol rol on rol.id_rol = usu.id_rol
	left join 
		configuracion_estado_area area on area.id_area = usu.id_area
	left join 
		configuracion_estado estado on estado.id_estado = usu.id_estado
	where 
		id_usuario = p_id_usuario;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_estado`
--

CREATE TABLE `configuracion_estado` (
  `id_estado` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_estado`
--

INSERT INTO `configuracion_estado` (`id_estado`, `descripcion`) VALUES
(1, 'Activo'),
(2, 'Inactivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_estado_area`
--

CREATE TABLE `configuracion_estado_area` (
  `id_area` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_estado_area`
--

INSERT INTO `configuracion_estado_area` (`id_area`, `descripcion`) VALUES
(1, 'Mantenimiento'),
(2, 'Produccion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_ingreso`
--

CREATE TABLE `configuracion_ingreso` (
  `fecha` date DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `dispositivo` varchar(50) DEFAULT NULL,
  `usuario` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_ingreso`
--

INSERT INTO `configuracion_ingreso` (`fecha`, `ip`, `hora_entrada`, `hora_salida`, `dispositivo`, `usuario`) VALUES
('2025-02-27', '127.0.0.1', '17:27:36', NULL, 'Desktop', 'Juan Meneses Ortega'),
('2025-02-27', '127.0.0.1', '17:28:16', NULL, 'Desktop', 'Juan Meneses Ortega'),
('2025-02-27', '127.0.0.1', '17:49:56', NULL, 'Desktop', 'Juan Meneses Ortega'),
('2025-02-27', '127.0.0.1', '18:01:22', NULL, 'Desktop', 'Juan Meneses Ortega'),
('2025-02-27', '127.0.0.1', '18:05:10', NULL, 'Desktop', 'Juan Meneses Ortega'),
('2025-02-27', '127.0.0.1', '18:07:04', NULL, 'Desktop', 'Juan Meneses Ortega');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_usuario`
--

CREATE TABLE `configuracion_usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `pwd` varchar(50) NOT NULL,
  `id_estado` int(11) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `id_area` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_usuario`
--

INSERT INTO `configuracion_usuario` (`id_usuario`, `nombre`, `correo`, `usuario`, `pwd`, `id_estado`, `id_rol`, `id_area`) VALUES
(1, 'Juan Meneses Ortega', 'tsu.juan.meneses@hotmail.com	', 'admin', '1234', 1, 1, 1),
(4, 'OrestesGaelUwU', 'gael@gmail.com', 'Gael', 'Gael1234', 1, 2, 1),
(5, 'LORENA MENESES ORTEGA ', 'lorena-meneses.ortega@hotmail.com', 'LORENA ', '1234', 1, 2, 1),
(6, 'MARISOL', 'MARISOL@GOLDENRED.COM', 'MARISOOL', '1234', 1, 2, 1),
(7, 'GUADALUPE', 'GUADA@GOLDENRED.COM', 'GUADALUPE312', '1234', 1, 2, 1),
(13, 'Abigail', 'aby@goldenred.com', 'aby', '12345', 1, 2, 1),
(14, 'jesus', 'jesusandree@goldenred.com', 'andree', '1234567', 1, 2, 1),
(15, 'jona', 'jonaarteaga@goldenred.com', 'jona', '123456', 1, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_usuario_rol`
--

CREATE TABLE `configuracion_usuario_rol` (
  `id_rol` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_usuario_rol`
--

INSERT INTO `configuracion_usuario_rol` (`id_rol`, `descripcion`) VALUES
(1, 'Administrador'),
(2, 'Operario');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuracion_estado`
--
ALTER TABLE `configuracion_estado`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `configuracion_estado_area`
--
ALTER TABLE `configuracion_estado_area`
  ADD PRIMARY KEY (`id_area`);

--
-- Indices de la tabla `configuracion_usuario`
--
ALTER TABLE `configuracion_usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `fk_estado_usuario` (`id_estado`),
  ADD KEY `fk_rol_usuario` (`id_rol`) USING BTREE,
  ADD KEY `fk_area_usuario` (`id_area`);

--
-- Indices de la tabla `configuracion_usuario_rol`
--
ALTER TABLE `configuracion_usuario_rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configuracion_estado`
--
ALTER TABLE `configuracion_estado`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `configuracion_estado_area`
--
ALTER TABLE `configuracion_estado_area`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `configuracion_usuario`
--
ALTER TABLE `configuracion_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `configuracion_usuario_rol`
--
ALTER TABLE `configuracion_usuario_rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `configuracion_usuario`
--
ALTER TABLE `configuracion_usuario`
  ADD CONSTRAINT `fk_area_usuario` FOREIGN KEY (`id_area`) REFERENCES `configuracion_estado_area` (`id_area`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_estado_usuario` FOREIGN KEY (`id_estado`) REFERENCES `configuracion_estado` (`id_estado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rol_usuario` FOREIGN KEY (`id_rol`) REFERENCES `configuracion_usuario_rol` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
