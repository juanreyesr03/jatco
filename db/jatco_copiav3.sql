-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-03-2025 a las 14:43:01
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

CREATE DEFINER=`jatco_beta`@`%` PROCEDURE `editar_usuario` (IN `p_id_usuario` INT, IN `p_nombre` VARCHAR(250), IN `p_correo` VARCHAR(100), IN `p_usuario` VARCHAR(100), IN `p_pwd` VARCHAR(100), IN `p_rol` INT)   BEGIN
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
                    configuracion_estado_area area ON area.id_area = usu.id_area
                WHERE 
                    usu.usuario = p_usuario
                LIMIT 1;

                -- Verificar estado del usuario
                IF v_id_estado = 2 THEN
                    SELECT 'Usuario inactivo' AS mensaje;
                ELSEIF (v_id_area IN (1, 2) AND v_id_rol IN (1, 2)) THEN
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
                    configuracion_estado_area area ON area.id_area = usu.id_area
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
	select * from configuracion_estado_area;
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
		configuracion_estado_area area on area.id_area = usu.id_area
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
('2025-03-11', '127.0.0.1', '18:05:42', NULL, 'Desktop', 'Juan Miguel Reyes');

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
(21, 'Kassandra Montserrat Hernandez Nava', 'hernandez.montse.18prog@gmail.com', 'montse05', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 1, 2, 2);

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
(3, '7501056360429', 'Talco Rexona', 1);

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
-- Indices de la tabla `escaneo_numero_parte`
--
ALTER TABLE `escaneo_numero_parte`
  ADD PRIMARY KEY (`id_numero_parte`),
  ADD KEY `fk_estado_parte` (`id_estado`);

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
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `configuracion_usuario_rol`
--
ALTER TABLE `configuracion_usuario_rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `escaneo_numero_parte`
--
ALTER TABLE `escaneo_numero_parte`
  MODIFY `id_numero_parte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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

--
-- Filtros para la tabla `escaneo_numero_parte`
--
ALTER TABLE `escaneo_numero_parte`
  ADD CONSTRAINT `fk_estado_parte` FOREIGN KEY (`id_estado`) REFERENCES `configuracion_estado` (`id_estado`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
