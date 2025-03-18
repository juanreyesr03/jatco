-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-03-2025 a las 01:10:50
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
CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `buscar_parte` (IN `p_numero_parte` VARCHAR(255), IN `p_nombre` VARCHAR(255))   BEGIN
    SELECT 
        numero_parte, 
        nombre
    FROM 
        escaneo_numero_parte
    WHERE 
        (numero_parte LIKE CONCAT('%', p_numero_parte, '%') OR p_numero_parte IS NULL)
        AND (nombre LIKE CONCAT('%', p_nombre, '%') OR p_nombre IS NULL);
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `buscar_usuario` (IN `p_filtro_nombre` VARCHAR(255), IN `p_filtro_correo` VARCHAR(255), IN `p_filtro_usuario` VARCHAR(50))   BEGIN
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

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `editar_parte` (IN `p_id_numero_parte` INT, IN `p_numero_parte` VARCHAR(250), IN `p_nombre` VARCHAR(250), IN `p_id_estado` INT)   BEGIN
	UPDATE escaneo_numero_parte 
    SET numero_parte = p_numero_parte,
    nombre = p_nombre,
    id_estado = p_id_estado
    WHERE id_numero_parte = p_id_numero_parte;
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `editar_usuario` (IN `p_id_usuario` INT, IN `p_nombre` VARCHAR(250), IN `p_correo` VARCHAR(100), IN `p_usuario` VARCHAR(100), IN `p_pwd` VARCHAR(100), IN `p_rol` INT, IN `p_area` INT)   BEGIN
    -- Actualizar datos del cliente en la tabla cliente_datos
    UPDATE configuracion_usuario
    SET 
		nombre = p_nombre,
        correo = p_correo,
        usuario = p_usuario,
        pwd = p_pwd,
        id_estado = 1,
        id_rol = p_rol,
        id_area = p_area
    WHERE id_usuario = p_id_usuario;
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `eliminar_parte` (IN `p_id_numero_parte` INT)   BEGIN
	DELETE FROM escaneo_numero_parte WHERE id_numero_parte = p_id_numero_parte;
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `eliminar_usuario` (IN `p_id_usuario` INT)   BEGIN   
    -- Then, delete the user from configuracion_usuario
    DELETE FROM configuracion_usuario WHERE id_usuario = p_id_usuario;
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `iniciar_sesion` (IN `p_usuario` VARCHAR(50), IN `p_pwd` VARCHAR(255), IN `p_platform` VARCHAR(50))   BEGIN
    DECLARE v_usuario_existe INT DEFAULT 0;
    DECLARE v_id_usuario INT;
    DECLARE v_nombre_usuario VARCHAR(50);
    DECLARE v_correo VARCHAR(100);
    DECLARE v_usuario VARCHAR(50);
    DECLARE v_pwd_hashed VARCHAR(255);
    DECLARE v_id_estado INT;
    DECLARE v_id_estado_descripcion VARCHAR(100);
    DECLARE v_id_rol INT;
    DECLARE v_id_rol_descripcion VARCHAR(100);
    DECLARE v_id_area INT;
    DECLARE v_id_area_descripcion VARCHAR(100);

    -- Verificar si el usuario existe y obtener la contraseña
    SELECT COUNT(*), pwd
    INTO v_usuario_existe, v_pwd_hashed
    FROM configuracion_usuario 
    WHERE usuario = p_usuario;

    -- Si el usuario existe
    IF v_usuario_existe > 0 THEN
        -- Comparar la contraseña encriptada
        IF v_pwd_hashed = SHA2(p_pwd, 256) THEN
            -- Verificar la plataforma
            IF p_platform = 'mobile' THEN
                -- Obtener los datos del usuario para la app móvil
                SELECT 
                    usu.id_usuario, 
                    usu.nombre, 
                    usu.correo, 
                    usu.usuario, 
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
                    configuracion_area area ON area.id_area = usu.id_area
                WHERE 
                    usu.usuario = p_usuario
                LIMIT 1;

                -- Verificar estado del usuario
                IF v_id_estado = 2 THEN
                    SELECT 'Usuario inactivo' AS mensaje;
                ELSEIF (v_id_area IN (1, 2, 3) AND v_id_rol IN (1, 2)) THEN
                    SELECT 'Inicio de sesión exitoso' AS mensaje,
                           v_id_usuario AS id_usuario,
                           v_nombre_usuario AS nombre,
                           v_id_rol AS id_rol,
                           v_id_rol_descripcion AS rol_descripcion,
                           v_id_estado AS id_estado,
                           v_id_area AS id_area;
                ELSE
                    SELECT CONCAT('Tu usuario es: ', v_id_area_descripcion, ', no puedes ingresar') AS mensaje;
                END IF;
            ELSEIF p_platform = 'web' THEN
                -- Obtener los datos del usuario para la app web
                SELECT 
                    usu.id_usuario, 
                    usu.nombre, 
                    usu.correo, 
                    usu.usuario, 
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
                    configuracion_area area ON area.id_area = usu.id_area
                WHERE 
                    usu.usuario = p_usuario
                LIMIT 1;

                -- Verificar estado del usuario
                IF v_id_estado = 2 THEN
                    SELECT 'Usuario inactivo' AS mensaje;
                ELSEIF v_id_area = 1 AND v_id_rol = 1 THEN
                    SELECT 'Inicio de sesión exitoso' AS mensaje,
                           v_id_usuario AS id_usuario,
                           v_nombre_usuario AS nombre,
                           v_id_rol AS id_rol,
                           v_id_rol_descripcion AS rol_descripcion,
                           v_id_estado AS id_estado,
                           v_id_area AS id_area;
                ELSE
                    SELECT CONCAT('Tu usuario es: ', v_id_area_descripcion, ', no puedes ingresar') AS mensaje;
                END IF;
            ELSE
                SELECT 'Acceso solo permitido desde la app móvil o web' AS mensaje;
            END IF;
        ELSE
            SELECT 'Credenciales incorrectas' AS mensaje;
        END IF;
    ELSE
        SELECT 'Credenciales incorrectas' AS mensaje;
    END IF;
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `insertar_configuracion_ingreso` (IN `p_fecha` DATE, IN `p_ip` VARCHAR(50), IN `p_hora_entrada` TIME, IN `p_id_usuario` INT, IN `p_dispositivo` VARCHAR(250))   BEGIN
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

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `insertar_llegada_proveedor` (IN `p_usuario` VARCHAR(250), IN `p_numero_parte` VARCHAR(250), IN `p_mensaje` VARCHAR(250))   BEGIN
	DECLARE p_hora TIME;
	DECLARE p_fecha DATE;
    DECLARE p_validar VARCHAR(1);
    
    SET p_hora = CURTIME();
    SET p_fecha = CURDATE();

	IF p_usuario = 'Número de Parte encontrada con exito' THEN
        SET p_validar = '1';
    ELSE
        SET p_validar = '0';
    END IF;
    
	INSERT INTO `llegada_proveedor`(`usuario`, `numero_parte`, `hora`, `fecha`, `mensaje`, `validar`) VALUES (
    p_mensaje, p_numero_parte, p_hora, p_fecha, p_usuario, p_validar);
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `insertar_llegada_proveedor_rack` (IN `p_usuario` VARCHAR(250), IN `p_rack` VARCHAR(250), IN `p_numero_parte` VARCHAR(250), IN `p_mensaje` VARCHAR(250) CHARACTER SET utf8mb4)   BEGIN
    DECLARE p_hora TIME;
    DECLARE p_fecha DATE;
    DECLARE p_validar VARCHAR(1);

    SET p_hora = CURTIME();
    SET p_fecha = CURDATE();

    -- Verificar si el mensaje es 'Rack válido'
    IF p_mensaje = 'Rack válido' THEN
        SET p_validar = '1';
    ELSE
        SET p_validar = '0';
    END IF;

    -- Insertar los valores en la tabla
    INSERT INTO `llegada_proveedor_rack`(`usuario`, `rack`, `numero_parte`, `hora`, `fecha`, `mensaje`, `validar`) 
    VALUES (p_usuario, p_numero_parte, p_rack, p_hora, p_fecha, p_mensaje, p_validar);
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `insertar_parte` (IN `p_numero_parte` VARCHAR(250), IN `p_nombre` VARCHAR(250))   BEGIN
	insert into escaneo_numero_parte (numero_parte, nombre, id_estado) values(p_numero_parte,p_nombre,1);
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `insertar_usuario` (IN `p_nombre` VARCHAR(255), IN `p_correo` VARCHAR(255), IN `p_usuario` VARCHAR(50), IN `p_pwd` VARCHAR(255), IN `p_id_rol` INT, IN `p_id_area` INT)   BEGIN
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
        SHA2(p_pwd, 256),  -- Encripta la contraseña con SHA-256
        1, 
        p_id_rol,
        p_id_area
    );
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `mostrar_configuracion_area` ()   BEGIN
	select * from configuracion_area;
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `mostrar_configuracion_usuario_rol` ()   BEGIN
	select * from configuracion_usuario_rol;
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `mostrar_ingresos` ()   BEGIN
	select * from configuracion_ingreso;
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `mostrar_partes` ()   BEGIN
	select 
		s.id_numero_parte,
        s.numero_parte,
        s.nombre,
        s.id_estado,
        c.descripcion
	from escaneo_numero_parte s
    left join configuracion_estado c on c.id_estado = s.id_estado;
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `mostrar_partes_id` (IN `p_parte_id` INT)   BEGIN
	SELECT 
		id_numero_parte,
        numero_parte, 
        nombre,
        id_estado
    FROM 
        escaneo_numero_parte
	WHERE 
		id_numero_parte = p_parte_id;
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `mostrar_partes_numero` (IN `p_numero_parte` VARCHAR(250))   BEGIN
	SELECT 
		id_numero_parte,
        numero_parte, 
        nombre,
        id_estado
    FROM 
        escaneo_numero_parte
	WHERE 
		numero_parte = p_numero_parte;
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `mostrar_usuarios` ()   BEGIN
	select 
		usu.id_usuario, 
		usu.nombre, 
		usu.correo, 
		usu.usuario, 
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
		configuracion_area area on area.id_area = usu.id_area
	left join
		configuracion_estado estado on estado.id_estado = usu.id_estado;
END$$

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `mostrar_usuario_id` (IN `p_id_usuario` INT)   BEGIN
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
		configuracion_area area on area.id_area = usu.id_area
	left join 
		configuracion_estado estado on estado.id_estado = usu.id_estado
	where 
		id_usuario = p_id_usuario;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_area`
--

CREATE TABLE `configuracion_area` (
  `id_area` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_area`
--

INSERT INTO `configuracion_area` (`id_area`, `descripcion`) VALUES
(1, 'Llegada'),
(2, 'Modulacion'),
(3, 'Suministro');

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
('2025-02-27', '127.0.0.1', '18:07:04', NULL, 'Desktop', 'Juan Meneses Ortega'),
('2025-03-03', '127.0.0.1', '16:44:04', NULL, 'Desktop', 'Juan Meneses Ortega'),
('2025-03-04', '127.0.0.1', '22:34:47', NULL, 'Desktop', 'Juan Meneses Ortega'),
('2025-03-04', '127.0.0.1', '22:39:17', NULL, 'Desktop', 'Juan Meneses Ortega'),
('2025-03-09', '127.0.0.1', '15:23:27', NULL, 'Desktop', 'Juan Meneses Ortega'),
('2025-03-09', '127.0.0.1', '15:38:48', NULL, 'Desktop', 'Juan Miguel Reyes'),
('2025-03-09', '127.0.0.1', '15:51:15', NULL, 'Desktop', 'Juan Miguel Reyes'),
('2025-03-09', '127.0.0.1', '15:51:28', NULL, 'Desktop', 'Juan Miguel Reyes'),
('2025-03-09', '127.0.0.1', '15:52:35', NULL, 'Desktop', 'Juan Miguel Reyes'),
('2025-03-11', '127.0.0.1', '11:27:16', NULL, 'Desktop', 'Juan Miguel Reyes'),
('2025-03-11', '127.0.0.1', '18:05:42', NULL, 'Desktop', 'Juan Miguel Reyes'),
('2025-03-13', '192.168.63.141', '18:20:03', NULL, 'Móvil', 'Juan Miguel Reyes'),
('2025-03-13', '127.0.0.1', '18:21:36', NULL, 'Desktop', 'Juan Miguel Reyes'),
('2025-03-14', '127.0.0.1', '08:14:27', NULL, 'Desktop', 'Juan Miguel Reyes'),
('2025-03-14', '10.20.57.27', '10:04:00', NULL, 'Desktop', 'Juan Miguel Reyes'),
('2025-03-14', '127.0.0.1', '14:57:07', NULL, 'Desktop', 'Juan Miguel Reyes'),
('2025-03-17', '127.0.0.1', '15:11:05', NULL, 'Desktop', 'Juan Miguel Reyes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_usuario`
--

CREATE TABLE `configuracion_usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `pwd` varchar(256) NOT NULL,
  `id_estado` int(11) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `id_area` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_usuario`
--

INSERT INTO `configuracion_usuario` (`id_usuario`, `nombre`, `correo`, `usuario`, `pwd`, `id_estado`, `id_rol`, `id_area`) VALUES
(20, 'Juan Miguel Reyes', 'juanmiguelreyesrobledo@gmail.com', 'jreyes', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 1, 1, 1),
(21, 'Kassandra Montserrat Hernandez Nava', 'hernandez.montse.18prog@gmail.com', 'montse05', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 1, 2, 2),
(23, 'Gerardo Diaz Vela', 'gerardo@gmail.com', 'geras', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 1, 2, 3);

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `escaneo_numero_parte`
--

CREATE TABLE `escaneo_numero_parte` (
  `id_numero_parte` int(11) NOT NULL,
  `numero_parte` varchar(250) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `escaneo_numero_parte`
--

INSERT INTO `escaneo_numero_parte` (`id_numero_parte`, `numero_parte`, `nombre`, `id_estado`) VALUES
(2, '75007614', 'Coca Cola 600ml', 1),
(3, '7501056360429', 'Talco Rexona', 1),
(4, '7501011123878', 'Doritos Incógnita', 1),
(5, '785120754483', 'CARIDOXEN', 1),
(6, '7503005995447', 'Gel Antibacterial', 1),
(7, '7506306214972', 'Desodorante Rexona', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llegada_proveedor`
--

CREATE TABLE `llegada_proveedor` (
  `id_reporte` int(11) NOT NULL,
  `usuario` varchar(250) NOT NULL,
  `numero_parte` varchar(250) NOT NULL,
  `hora` time NOT NULL,
  `fecha` date NOT NULL,
  `mensaje` varchar(300) NOT NULL,
  `validar` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `llegada_proveedor`
--

INSERT INTO `llegada_proveedor` (`id_reporte`, `usuario`, `numero_parte`, `hora`, `fecha`, `mensaje`, `validar`) VALUES
(1, 'jreyes', '75007614', '13:47:12', '2025-03-14', 'Número de Parte encontrada con exito', '1'),
(2, 'jreyes', '785120754483', '13:47:26', '2025-03-14', 'Número de Parte encontrada con exito', '1'),
(3, 'jreyes', '785120754483HAJ75007614', '13:47:26', '2025-03-14', 'El Número de Parte No Existe', '0'),
(4, 'jreyes', '785120754483', '13:47:30', '2025-03-14', 'Número de Parte encontrada con exito', '1'),
(5, 'jreyes', 'HAJ75007614', '13:47:37', '2025-03-14', 'El Número de Parte No Existe', '0'),
(6, 'jreyes', '75007614', '14:02:41', '2025-03-14', 'Número de Parte encontrada con exito', '1'),
(7, '', '75007614HAJ75007614', '14:02:42', '2025-03-14', 'El Número de Parte No Existe', '0'),
(8, '', '75007614', '14:02:45', '2025-03-14', 'Número de Parte encontrada con exito', '1'),
(9, 'jreyes', '785120754483', '14:08:32', '2025-03-14', 'Número de Parte encontrada con exito', '1'),
(10, 'jreyes', 'j', '15:10:50', '2025-03-17', 'El Número de Parte No Existe', '0'),
(11, 'jreyes', '7506306214972', '15:11:50', '2025-03-17', 'El Número de Parte No Existe', '0'),
(12, 'jreyes', '75063062149727506306214972', '15:11:51', '2025-03-17', 'El Número de Parte No Existe', '0'),
(13, 'jreyes', '7506306214972', '15:11:53', '2025-03-17', 'El Número de Parte No Existe', '0'),
(14, '', '7506306214972', '15:12:17', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(15, 'jreyes', '7506306214972', '16:28:22', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(16, 'jreyes', '7506306214972', '16:54:55', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(17, 'jreyes', '7506306214972', '16:55:03', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(18, 'jreyes', '75063062149727506306214972', '16:55:05', '2025-03-17', 'El Número de Parte No Existe', '0'),
(19, 'jreyes', '750630621497275063062149727506306214972', '16:55:05', '2025-03-17', 'El Número de Parte No Existe', '0'),
(20, 'jreyes', '4006381492263', '16:58:21', '2025-03-17', 'El Número de Parte No Existe', '0'),
(21, 'jreyes', '7506306214972', '17:05:43', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(22, 'jreyes', '7506306214972', '17:05:50', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(23, 'jreyes', '7506306214972', '17:08:28', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(24, 'jreyes', '7506306214972', '17:08:36', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(25, 'jreyes', '4006381492263', '17:09:01', '2025-03-17', 'El Número de Parte No Existe', '0'),
(26, 'jreyes', '7506306214972', '17:12:24', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(27, 'jreyes', '4006381492263', '17:12:31', '2025-03-17', 'El Número de Parte No Existe', '0'),
(28, 'jreyes', '4006381492263', '17:13:13', '2025-03-17', 'El Número de Parte No Existe', '0'),
(29, 'jreyes', '7506306214972', '17:15:06', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(30, 'jreyes', '4006381492263', '17:15:12', '2025-03-17', 'El Número de Parte No Existe', '0'),
(31, 'jreyes', '4006381492263', '17:16:56', '2025-03-17', 'El Número de Parte No Existe', '0'),
(32, 'jreyes', '7506306214972', '17:18:51', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(33, 'jreyes', '4006381492263', '17:19:08', '2025-03-17', 'El Número de Parte No Existe', '0'),
(34, 'jreyes', '7506306214972', '17:23:45', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(35, 'jreyes', '4006381492263', '17:24:07', '2025-03-17', 'El Número de Parte No Existe', '0'),
(36, 'jreyes', '7506306214972', '17:26:31', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(37, 'jreyes', '4006381492263', '17:26:35', '2025-03-17', 'El Número de Parte No Existe', '0'),
(38, 'jreyes', '4006381492263', '17:29:44', '2025-03-17', 'El Número de Parte No Existe', '0'),
(39, 'jreyes', '7506306214972', '17:29:55', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(40, 'jreyes', '7506306214972', '17:31:14', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(41, 'jreyes', '7506306214972', '17:31:28', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(42, 'jreyes', '4006381492263', '17:31:41', '2025-03-17', 'El Número de Parte No Existe', '0'),
(43, 'jreyes', '4006381492263', '17:32:15', '2025-03-17', 'El Número de Parte No Existe', '0'),
(44, 'jreyes', '4006381492263', '17:34:06', '2025-03-17', 'El Número de Parte No Existe', '0'),
(45, 'jreyes', '4006381492263', '17:34:42', '2025-03-17', 'El Número de Parte No Existe', '0'),
(46, 'jreyes', '7506306214972', '17:40:47', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(47, 'jreyes', '4006381492263', '17:41:32', '2025-03-17', 'El Número de Parte No Existe', '0'),
(48, 'jreyes', '4006381492263', '17:41:37', '2025-03-17', 'El Número de Parte No Existe', '0'),
(49, 'jreyes', '4006381492263', '17:42:28', '2025-03-17', 'El Número de Parte No Existe', '0'),
(50, 'jreyes', '7506306214972', '17:54:06', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(51, 'jreyes', '7506306214972', '17:54:29', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(52, 'jreyes', '4006381492263', '17:54:54', '2025-03-17', 'El Número de Parte No Existe', '0'),
(53, 'jreyes', 'http://bit.ly/3KGlxIs?r=qr', '18:07:02', '2025-03-17', 'El Número de Parte No Existe', '0'),
(54, 'jreyes', '7506306214972', '18:08:08', '2025-03-17', 'Número de Parte encontrada con exito', '1'),
(55, 'jreyes', '4006381492263', '18:08:15', '2025-03-17', 'El Número de Parte No Existe', '0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llegada_proveedor_rack`
--

CREATE TABLE `llegada_proveedor_rack` (
  `id_reporte` int(11) NOT NULL,
  `usuario` varchar(250) NOT NULL,
  `rack` varchar(250) NOT NULL,
  `numero_parte` varchar(250) NOT NULL,
  `hora` time NOT NULL,
  `fecha` date NOT NULL,
  `mensaje` varchar(250) NOT NULL,
  `validar` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `llegada_proveedor_rack`
--

INSERT INTO `llegada_proveedor_rack` (`id_reporte`, `usuario`, `rack`, `numero_parte`, `hora`, `fecha`, `mensaje`, `validar`) VALUES
(1, 'jreyes', 'HAJ75007614', '75007614', '13:47:15', '2025-03-14', 'Rack válido', '1'),
(2, 'jreyes', 'HAJ75007614', '785120754483', '13:47:31', '2025-03-14', 'El rack no coincide con el número de parte', '0'),
(3, 'jreyes', '785120754483', 'HAJ75007614', '13:47:39', '2025-03-14', 'El rack no coincide con el número de parte', '0'),
(4, '', 'HAJ75007614', '75007614', '14:02:47', '2025-03-14', 'Rack válido', '1'),
(5, 'jreyes', 'HA785120754483', '785120754483', '14:08:34', '2025-03-14', 'Rack válido', '1'),
(6, '', '7506306214972', '7506306214972', '15:12:20', '2025-03-17', 'Rack válido', '1'),
(7, 'jreyes', '7506306214972', '7506306214972', '16:28:24', '2025-03-17', 'Rack válido', '1'),
(8, 'jreyes', '7506306214972', '7506306214972', '16:54:58', '2025-03-17', 'Rack válido', '1'),
(9, 'jreyes', '7506306214972', '7506306214972', '17:12:24', '2025-03-17', 'Rack válido', '1'),
(10, 'jreyes', '4006381492263', '4006381492263', '17:12:32', '2025-03-17', 'Rack válido', '1'),
(11, 'jreyes', '4006381492263', '7506306214972', '17:15:09', '2025-03-17', 'El rack no coincide con el número de parte', '0'),
(12, 'jreyes', '4006381492263', '7506306214972', '17:18:57', '2025-03-17', 'El rack no coincide con el número de parte', '0'),
(13, 'jreyes', '4006381492263', '4006381492263', '17:19:10', '2025-03-17', 'Rack válido', '1'),
(14, 'jreyes', '7506306214972', '7506306214972', '17:31:15', '2025-03-17', 'Rack válido', '1'),
(15, 'jreyes', '4006381492263', '7506306214972', '17:31:30', '2025-03-17', 'El rack no coincide con el número de parte', '0'),
(16, 'jreyes', '7506306214972', '7506306214972', '17:40:48', '2025-03-17', 'Rack válido', '1'),
(17, 'jreyes', '4006381492263', '4006381492263', '17:41:33', '2025-03-17', 'Rack válido', '1'),
(18, 'jreyes', '40063814922634006381492263', '4006381492263', '17:41:33', '2025-03-17', 'Rack válido', '1'),
(19, 'jreyes', '400638149226340063814922634006381492263', '4006381492263', '17:41:34', '2025-03-17', 'Rack válido', '1'),
(20, 'jreyes', '4006381492263400638149226340063814922634006381492263', '4006381492263', '17:41:35', '2025-03-17', 'Rack válido', '1'),
(21, 'jreyes', '7506306214972', '7506306214972', '17:54:24', '2025-03-17', 'Rack válido', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `shutters`
--

CREATE TABLE `shutters` (
  `id_shutters` int(11) NOT NULL,
  `codigo_shutters` varchar(250) NOT NULL,
  `area` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuracion_area`
--
ALTER TABLE `configuracion_area`
  ADD PRIMARY KEY (`id_area`);

--
-- Indices de la tabla `configuracion_estado`
--
ALTER TABLE `configuracion_estado`
  ADD PRIMARY KEY (`id_estado`);

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
-- Indices de la tabla `escaneo_numero_parte`
--
ALTER TABLE `escaneo_numero_parte`
  ADD PRIMARY KEY (`id_numero_parte`),
  ADD KEY `fk_estado_parte` (`id_estado`);

--
-- Indices de la tabla `llegada_proveedor`
--
ALTER TABLE `llegada_proveedor`
  ADD PRIMARY KEY (`id_reporte`);

--
-- Indices de la tabla `llegada_proveedor_rack`
--
ALTER TABLE `llegada_proveedor_rack`
  ADD PRIMARY KEY (`id_reporte`);

--
-- Indices de la tabla `shutters`
--
ALTER TABLE `shutters`
  ADD PRIMARY KEY (`id_shutters`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configuracion_area`
--
ALTER TABLE `configuracion_area`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `configuracion_estado`
--
ALTER TABLE `configuracion_estado`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `configuracion_usuario`
--
ALTER TABLE `configuracion_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `configuracion_usuario_rol`
--
ALTER TABLE `configuracion_usuario_rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `escaneo_numero_parte`
--
ALTER TABLE `escaneo_numero_parte`
  MODIFY `id_numero_parte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `llegada_proveedor`
--
ALTER TABLE `llegada_proveedor`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `llegada_proveedor_rack`
--
ALTER TABLE `llegada_proveedor_rack`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `shutters`
--
ALTER TABLE `shutters`
  MODIFY `id_shutters` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `configuracion_usuario`
--
ALTER TABLE `configuracion_usuario`
  ADD CONSTRAINT `fk_area_usuario` FOREIGN KEY (`id_area`) REFERENCES `configuracion_area` (`id_area`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_estado_usuario` FOREIGN KEY (`id_estado`) REFERENCES `configuracion_estado` (`id_estado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rol_usuario` FOREIGN KEY (`id_rol`) REFERENCES `configuracion_usuario_rol` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `escaneo_numero_parte`
--
ALTER TABLE `escaneo_numero_parte`
  ADD CONSTRAINT `fk_estado_parte` FOREIGN KEY (`id_estado`) REFERENCES `configuracion_estado` (`id_estado`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
