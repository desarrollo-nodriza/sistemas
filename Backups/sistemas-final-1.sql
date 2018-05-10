-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-03-2017 a las 20:47:05
-- Versión del servidor: 5.7.14
-- Versión de PHP: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistemas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_administradores`
--

CREATE TABLE `rp_administradores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `google_id` varchar(100) DEFAULT NULL,
  `google_dominio` varchar(100) DEFAULT NULL,
  `google_nombre` varchar(100) DEFAULT NULL,
  `google_apellido` char(20) DEFAULT NULL,
  `google_imagen` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime NOT NULL,
  `rol_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_categorias`
--

CREATE TABLE `rp_categorias` (
  `id` bigint(20) NOT NULL,
  `tienda_id` smallint(6) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text,
  `imagen` varchar(100) DEFAULT NULL,
  `color_fondo` varchar(10) DEFAULT NULL,
  `color_titulo` varchar(10) DEFAULT NULL,
  `color_parrafo` varchar(10) DEFAULT NULL,
  `tres_columnas` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `orden` int(11) NOT NULL DEFAULT '0',
  `orden_productos` varchar(60) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_categorias_emails`
--

CREATE TABLE `rp_categorias_emails` (
  `id` bigint(20) NOT NULL,
  `email_id` bigint(20) UNSIGNED NOT NULL,
  `categoria_id` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_categorias_productos`
--

CREATE TABLE `rp_categorias_productos` (
  `id` bigint(20) NOT NULL,
  `producto_id` bigint(20) NOT NULL,
  `categoria_id` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_categorias_productotiendas`
--

CREATE TABLE `rp_categorias_productotiendas` (
  `id` bigint(20) NOT NULL,
  `categoria_id` bigint(20) NOT NULL,
  `id_product` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_categorias_toolmanias`
--

CREATE TABLE `rp_categorias_toolmanias` (
  `id` bigint(20) NOT NULL,
  `categoria_id` bigint(20) NOT NULL,
  `id_product` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_cotizaciones`
--

CREATE TABLE `rp_cotizaciones` (
  `id` bigint(20) NOT NULL,
  `tienda_id` smallint(6) NOT NULL,
  `moneda_id` smallint(6) DEFAULT NULL,
  `estado_cotizacion_id` smallint(6) DEFAULT NULL,
  `prospecto_id` bigint(20) DEFAULT NULL,
  `validez_fecha_id` smallint(6) DEFAULT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `id_address` int(11) DEFAULT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text,
  `comentarios` text,
  `email_cliente` varchar(50) NOT NULL,
  `nombre_cliente` varchar(200) NOT NULL,
  `fono_cliente` varchar(10) DEFAULT NULL,
  `direccion_cliente` varchar(200) DEFAULT NULL,
  `asunto_cliente` varchar(200) DEFAULT NULL,
  `rut_empresa_cliente` varchar(12) DEFAULT NULL,
  `nombre_empresa_cliente` varchar(50) DEFAULT NULL,
  `fecha_cotizacion` varchar(30) NOT NULL,
  `envio_cotizacion` varchar(200) DEFAULT NULL,
  `vendedor` varchar(100) NOT NULL,
  `email_vendedor` varchar(50) DEFAULT NULL,
  `total_neto` varchar(100) NOT NULL,
  `descuento` varchar(20) DEFAULT NULL,
  `iva` varchar(100) NOT NULL,
  `total_bruto` varchar(100) NOT NULL,
  `enviado` tinyint(1) NOT NULL DEFAULT '0',
  `generado` tinyint(1) NOT NULL DEFAULT '0',
  `archivo` varchar(200) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_emails`
--

CREATE TABLE `rp_emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tienda_id` bigint(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `descripcion` text,
  `fecha` varchar(50) DEFAULT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `html` text,
  `ultimo_html` longtext,
  `sitio_url` varchar(300) NOT NULL,
  `mostrar_cuotas` tinyint(1) DEFAULT NULL,
  `cuotas` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `plantilla_id` bigint(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_estado_cotizaciones`
--

CREATE TABLE `rp_estado_cotizaciones` (
  `id` smallint(6) NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_estado_prospectos`
--

CREATE TABLE `rp_estado_prospectos` (
  `id` smallint(6) NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_graficos`
--

CREATE TABLE `rp_graficos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `tipo_grafico` int(11) NOT NULL COMMENT '{"1": "Linea", "2" : "barra", "3" : "Area", "4", "Donuts", "5" : "Recuadro", "6" : "Slider"}',
  `slug` varchar(100) NOT NULL,
  `descipcion` text,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_graficos_reportes`
--

CREATE TABLE `rp_graficos_reportes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reporte_id` bigint(20) UNSIGNED NOT NULL,
  `grafico_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_logs`
--

CREATE TABLE `rp_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `administrador` varchar(25) NOT NULL,
  `administrador_id` bigint(20) UNSIGNED NOT NULL,
  `modulo` varchar(25) NOT NULL,
  `modulo_accion` varchar(25) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_modulos`
--

CREATE TABLE `rp_modulos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre` varchar(25) NOT NULL,
  `url` varchar(100) DEFAULT NULL,
  `icono` varchar(30) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_modulos_roles`
--

CREATE TABLE `rp_modulos_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rol_id` bigint(20) UNSIGNED NOT NULL,
  `modulo_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_monedas`
--

CREATE TABLE `rp_monedas` (
  `id` smallint(6) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_origenes`
--

CREATE TABLE `rp_origenes` (
  `id` smallint(6) NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_plantillas`
--

CREATE TABLE `rp_plantillas` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `desripcion` text,
  `imagen` varchar(100) DEFAULT NULL,
  `html` text,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_productos`
--

CREATE TABLE `rp_productos` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text,
  `modelo` varchar(10) DEFAULT NULL,
  `imagen` varchar(400) NOT NULL,
  `valor` varchar(30) NOT NULL,
  `porcentaje_oferta` int(11) DEFAULT NULL,
  `boton` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(400) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `orden` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_productotiendas_cotizaciones`
--

CREATE TABLE `rp_productotiendas_cotizaciones` (
  `id` bigint(20) NOT NULL,
  `id_product` bigint(20) NOT NULL,
  `cotizacion_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `nombre_descuento` varchar(50) DEFAULT NULL,
  `descuento` varchar(10) DEFAULT NULL,
  `precio_neto` varchar(20) NOT NULL,
  `total_neto` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_productotiendas_prospectos`
--

CREATE TABLE `rp_productotiendas_prospectos` (
  `id` bigint(20) NOT NULL,
  `id_product` bigint(20) NOT NULL,
  `prospecto_id` bigint(20) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `nombre_descuento` varchar(50) DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `precio_neto` varchar(20) DEFAULT NULL,
  `total_neto` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_productotienda_descuentos`
--

CREATE TABLE `rp_productotienda_descuentos` (
  `id_productotienda_descuento` int(11) NOT NULL,
  `id_product` bigint(20) NOT NULL,
  `tienda_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descuento` int(11) NOT NULL,
  `prospecto` tinyint(1) NOT NULL DEFAULT '0',
  `cotizacion` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_prospectos`
--

CREATE TABLE `rp_prospectos` (
  `id` bigint(20) NOT NULL,
  `tienda_id` smallint(6) NOT NULL,
  `estado_prospecto_id` smallint(6) DEFAULT NULL,
  `moneda_id` smallint(6) DEFAULT NULL,
  `origen_id` smallint(6) DEFAULT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `id_address` int(11) DEFAULT NULL,
  `existente` tinyint(1) NOT NULL DEFAULT '0',
  `nombre` varchar(50) NOT NULL,
  `descripcion` text NOT NULL,
  `comentarios` longtext,
  `descuento` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_reportes`
--

CREATE TABLE `rp_reportes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `tienda_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_roles`
--

CREATE TABLE `rp_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `permisos` text NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_tiendas`
--

CREATE TABLE `rp_tiendas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `url` varchar(100) NOT NULL,
  `configuracion` varchar(50) NOT NULL,
  `prefijo` varchar(50) NOT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `tema` varchar(50) DEFAULT NULL,
  `logo` varchar(200) DEFAULT NULL,
  `nombre_fantasia` varchar(100) NOT NULL,
  `rut` varchar(12) NOT NULL,
  `direccion` varchar(300) NOT NULL,
  `giro` varchar(100) NOT NULL,
  `fono` varchar(9) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_validez_fechas`
--

CREATE TABLE `rp_validez_fechas` (
  `id` smallint(6) NOT NULL,
  `valor` varchar(50) NOT NULL,
  `comentario` text,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `rp_administradores`
--
ALTER TABLE `rp_administradores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_Relationship6` (`rol_id`);

--
-- Indices de la tabla `rp_categorias`
--
ALTER TABLE `rp_categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_categorias_emails`
--
ALTER TABLE `rp_categorias_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Relationship11` (`email_id`),
  ADD KEY `Relationship12` (`categoria_id`);

--
-- Indices de la tabla `rp_categorias_productos`
--
ALTER TABLE `rp_categorias_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Relationship13` (`producto_id`),
  ADD KEY `Relationship14` (`categoria_id`);

--
-- Indices de la tabla `rp_categorias_productotiendas`
--
ALTER TABLE `rp_categorias_productotiendas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_categorias_toolmanias`
--
ALTER TABLE `rp_categorias_toolmanias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `toolmania_id` (`id_product`);

--
-- Indices de la tabla `rp_cotizaciones`
--
ALTER TABLE `rp_cotizaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_Relationship7` (`moneda_id`),
  ADD KEY `IX_Relationship8` (`estado_cotizacion_id`),
  ADD KEY `IX_Relationship9` (`prospecto_id`),
  ADD KEY `IX_Relationship10` (`validez_fecha_id`);

--
-- Indices de la tabla `rp_emails`
--
ALTER TABLE `rp_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_Relationship7` (`plantilla_id`);

--
-- Indices de la tabla `rp_estado_cotizaciones`
--
ALTER TABLE `rp_estado_cotizaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_estado_prospectos`
--
ALTER TABLE `rp_estado_prospectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_graficos`
--
ALTER TABLE `rp_graficos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_graficos_reportes`
--
ALTER TABLE `rp_graficos_reportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Relationship8` (`reporte_id`),
  ADD KEY `Relationship9` (`grafico_id`);

--
-- Indices de la tabla `rp_logs`
--
ALTER TABLE `rp_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_modulos`
--
ALTER TABLE `rp_modulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_modulos_roles`
--
ALTER TABLE `rp_modulos_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Relationship1` (`rol_id`),
  ADD KEY `Relationship2` (`modulo_id`);

--
-- Indices de la tabla `rp_monedas`
--
ALTER TABLE `rp_monedas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_origenes`
--
ALTER TABLE `rp_origenes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_plantillas`
--
ALTER TABLE `rp_plantillas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_productos`
--
ALTER TABLE `rp_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_productotiendas_cotizaciones`
--
ALTER TABLE `rp_productotiendas_cotizaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_productotiendas_prospectos`
--
ALTER TABLE `rp_productotiendas_prospectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_productotienda_descuentos`
--
ALTER TABLE `rp_productotienda_descuentos`
  ADD PRIMARY KEY (`id_productotienda_descuento`),
  ADD KEY `prospecto_id` (`id_product`);

--
-- Indices de la tabla `rp_prospectos`
--
ALTER TABLE `rp_prospectos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_Relationship4` (`estado_prospecto_id`),
  ADD KEY `IX_Relationship5` (`moneda_id`),
  ADD KEY `IX_Relationship6` (`origen_id`);

--
-- Indices de la tabla `rp_reportes`
--
ALTER TABLE `rp_reportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_Relationship7` (`tienda_id`);

--
-- Indices de la tabla `rp_roles`
--
ALTER TABLE `rp_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_tiendas`
--
ALTER TABLE `rp_tiendas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rp_validez_fechas`
--
ALTER TABLE `rp_validez_fechas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `rp_administradores`
--
ALTER TABLE `rp_administradores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT de la tabla `rp_categorias`
--
ALTER TABLE `rp_categorias`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT de la tabla `rp_categorias_emails`
--
ALTER TABLE `rp_categorias_emails`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1470;
--
-- AUTO_INCREMENT de la tabla `rp_categorias_productos`
--
ALTER TABLE `rp_categorias_productos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=337;
--
-- AUTO_INCREMENT de la tabla `rp_categorias_productotiendas`
--
ALTER TABLE `rp_categorias_productotiendas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `rp_categorias_toolmanias`
--
ALTER TABLE `rp_categorias_toolmanias`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303;
--
-- AUTO_INCREMENT de la tabla `rp_cotizaciones`
--
ALTER TABLE `rp_cotizaciones`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT de la tabla `rp_emails`
--
ALTER TABLE `rp_emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
--
-- AUTO_INCREMENT de la tabla `rp_estado_cotizaciones`
--
ALTER TABLE `rp_estado_cotizaciones`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `rp_estado_prospectos`
--
ALTER TABLE `rp_estado_prospectos`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `rp_graficos`
--
ALTER TABLE `rp_graficos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT de la tabla `rp_graficos_reportes`
--
ALTER TABLE `rp_graficos_reportes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT de la tabla `rp_logs`
--
ALTER TABLE `rp_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `rp_modulos`
--
ALTER TABLE `rp_modulos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT de la tabla `rp_modulos_roles`
--
ALTER TABLE `rp_modulos_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;
--
-- AUTO_INCREMENT de la tabla `rp_monedas`
--
ALTER TABLE `rp_monedas`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `rp_origenes`
--
ALTER TABLE `rp_origenes`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `rp_plantillas`
--
ALTER TABLE `rp_plantillas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `rp_productos`
--
ALTER TABLE `rp_productos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT de la tabla `rp_productotiendas_cotizaciones`
--
ALTER TABLE `rp_productotiendas_cotizaciones`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT de la tabla `rp_productotiendas_prospectos`
--
ALTER TABLE `rp_productotiendas_prospectos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;
--
-- AUTO_INCREMENT de la tabla `rp_productotienda_descuentos`
--
ALTER TABLE `rp_productotienda_descuentos`
  MODIFY `id_productotienda_descuento` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `rp_prospectos`
--
ALTER TABLE `rp_prospectos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;
--
-- AUTO_INCREMENT de la tabla `rp_reportes`
--
ALTER TABLE `rp_reportes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `rp_roles`
--
ALTER TABLE `rp_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `rp_tiendas`
--
ALTER TABLE `rp_tiendas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `rp_validez_fechas`
--
ALTER TABLE `rp_validez_fechas`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
