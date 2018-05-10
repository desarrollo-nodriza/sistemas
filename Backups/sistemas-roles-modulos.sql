-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-03-2017 a las 15:24:35
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

--
-- Volcado de datos para la tabla `rp_estado_prospectos`
--

INSERT INTO `rp_estado_prospectos` (`id`, `nombre`, `activo`) VALUES
(1, 'No asignado', 1),
(2, 'Iniciado', 1),
(3, 'Esperando información', 1),
(4, 'En progreso', 1),
(5, 'En espera', 1),
(6, 'Imposible contactar', 1),
(7, 'Finalizada', 1),
(8, 'Cancelada', 1);

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

--
-- Volcado de datos para la tabla `rp_modulos`
--

INSERT INTO `rp_modulos` (`id`, `parent_id`, `nombre`, `url`, `icono`, `activo`, `created`, `modified`) VALUES
(1, NULL, 'Super Usuario', '', 'fa fa-bug', 1, '2017-01-16 13:22:56', '2017-01-17 10:07:40'),
(2, 1, 'Módulos', 'modulos', 'fa fa-cubes', 1, '2017-01-16 14:10:50', '2017-01-16 16:31:41'),
(3, NULL, 'Newsletter', '', 'fa fa-envelope', 1, '2017-01-16 15:31:55', '2017-02-20 15:55:02'),
(4, 3, 'Newsletter', 'emails', 'fa fa-envelope-o', 1, '2017-01-16 15:32:39', '2017-02-20 16:19:57'),
(5, 3, 'Plantillas', 'plantillas', 'fa fa-file-text-o', 1, '2017-01-16 15:34:57', '2017-02-20 16:20:09'),
(6, 1, 'Administradores', 'administradores', 'fa fa-user', 1, '2017-01-16 15:39:07', '2017-01-23 11:04:12'),
(7, 1, 'Roles de usuario', 'roles', 'fa fa-flag-checkered', 1, '2017-01-16 15:40:45', '2017-01-23 11:04:21'),
(25, 1, 'Estado Prospectos', 'estadoprospectos', 'fa fa-cog', 1, '2017-02-24 14:00:45', '2017-02-24 14:00:45'),
(9, 3, 'Categorías', 'categorias', 'fa fa-list-alt', 1, '2017-01-16 16:58:43', '2017-02-20 16:20:31'),
(10, NULL, 'Ventas', '', 'fa fa-money', 1, '2017-01-17 10:54:12', '2017-02-24 14:04:23'),
(24, 1, 'Estado Cotizaciones', 'estadocotizaciones', 'fa fa-cog', 1, '2017-02-24 14:00:15', '2017-02-24 14:00:15'),
(12, 10, 'Gráficos', 'graficos', 'fa fa-bar-chart-o', 1, '2017-01-17 10:55:35', '2017-01-23 11:04:59'),
(13, NULL, 'Tiendas', 'tiendas', 'fa fa-globe', 1, '2017-01-17 10:56:28', '2017-02-24 12:50:29'),
(23, 3, 'Productos Tiendas', 'productotiendas', 'fa fa-shopping-bag', 1, '2017-02-24 12:40:07', '2017-02-24 12:40:07'),
(26, 10, 'Prospectos', 'prospectos', 'fa fa-bookmark', 1, '2017-02-24 14:01:45', '2017-03-28 12:15:47'),
(27, 10, 'Cotizaciones', 'cotizaciones', 'fa fa-lightbulb-o', 1, '2017-02-24 14:02:16', '2017-03-28 12:16:20'),
(28, 10, 'Monedas', 'monedas', 'fa fa-money', 1, '2017-02-24 14:03:13', '2017-03-28 12:15:59'),
(29, 10, 'Origenes', 'origenes', 'fa fa-sitemap', 1, '2017-02-24 14:03:37', '2017-03-28 12:16:37'),
(30, NULL, 'Clientes', 'clientes', 'fa fa-users', 1, '2017-02-24 16:56:11', '2017-03-28 12:17:19'),
(31, 10, 'Validez de la cotización', 'validezfechas', 'fa fa-calendar', 1, '2017-03-03 14:04:17', '2017-03-28 12:15:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_modulos_roles`
--

CREATE TABLE `rp_modulos_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rol_id` bigint(20) UNSIGNED NOT NULL,
  `modulo_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `rp_modulos_roles`
--

INSERT INTO `rp_modulos_roles` (`id`, `rol_id`, `modulo_id`) VALUES
(14, 1, 1),
(10, 1, 2),
(103, 4, 4),
(157, 1, 10),
(49, 1, 6),
(51, 1, 7),
(156, 2, 10),
(176, 1, 27),
(110, 4, 9),
(98, 4, 3),
(171, 1, 26),
(136, 1, 25),
(65, 1, 12),
(134, 1, 13),
(133, 2, 13),
(102, 3, 4),
(97, 3, 3),
(111, 5, 9),
(109, 3, 9),
(175, 2, 27),
(96, 2, 3),
(101, 2, 4),
(106, 5, 5),
(50, 5, 6),
(52, 5, 7),
(174, 5, 27),
(108, 2, 9),
(155, 5, 10),
(135, 1, 24),
(66, 5, 12),
(132, 5, 13),
(95, 1, 3),
(100, 1, 4),
(105, 1, 5),
(170, 2, 26),
(107, 1, 9),
(169, 5, 26),
(99, 5, 3),
(104, 5, 4),
(124, 1, 23),
(125, 2, 23),
(126, 3, 23),
(127, 4, 23),
(128, 5, 23),
(173, 1, 28),
(172, 5, 28),
(178, 1, 29),
(177, 5, 29),
(179, 1, 30),
(168, 1, 31),
(167, 5, 31);

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

--
-- Volcado de datos para la tabla `rp_roles`
--

INSERT INTO `rp_roles` (`id`, `nombre`, `permisos`, `activo`, `created`, `modified`) VALUES
(5, 'Administración', '{\r\n	"administradores" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0, "login" : 0, "logout": 0\r\n	},\r\n	"modulos" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"roles" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"emails" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"plantillas" : {\r\n		"index" : 1, "add": 0, "edit" : 0, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 0\r\n	},\r\n	"categorias" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"graficos" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"pages" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"reportes" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"tiendas" : {\r\n		"index" : 1, "add": 0, "edit" : 0, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 0\r\n	},\r\n	"pages" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"prospectos" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"cotizaciones" : {\r\n		"index" : 1, "add": 0, "edit" : 0, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"estadocotizaciones" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"estadoprospectos" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"monedas" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 0\r\n	},\r\n	"origenes" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"validezfechas" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"productotiendas" : {\r\n		"index" : 1, "add": 0, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"clientes" : {\r\n		"index" : 1, "add": 0, "edit" : 0, "delete" : 0, "view" : 1, "generate" : 0, "activate" : 0\r\n	},\r\n    "paises" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"orderses" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	}\r\n}', 1, '2017-01-23 10:24:41', '2017-03-28 12:23:29'),
(4, 'Gestión del Newsletter', '{\r\n	"administradores" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0, "login" : 0, "logout": 0\r\n	},\r\n	"modulos" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"roles" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"emails" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1, "generarHtml" : 1\r\n	},\r\n	"plantillas" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 1, "generate" : 0, "activate" : 0\r\n	},\r\n	"categorias" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 0, "activate" : 1\r\n	},\r\n	"toolmanias" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1, "associate" : 1\r\n	},\r\n	"graficos" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"pages" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"reportes" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"tiendas" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	}\r\n}', 1, '2017-01-23 10:24:13', '2017-01-24 11:47:42'),
(3, 'Mantención', '{\r\n	"administradores" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0, "login" : 0, "logout": 0\r\n	},\r\n	"modulos" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"roles" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"emails" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 0, "activate" : 1, "generarHtml" : 1\r\n	},\r\n	"plantillas" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 1, "generate" : 0, "activate" : 0\r\n	},\r\n	"categorias" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 0, "activate" : 1\r\n	},\r\n	"toolmanias" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1, "associate" : 1\r\n	},\r\n	"graficos" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"pages" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"reportes" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"tiendas" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	}\r\n}', 1, '2017-01-23 09:59:26', '2017-01-23 10:20:02'),
(2, 'Ventas', '{\r\n	"administradores" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0, "login" : 0, "logout": 0\r\n	},\r\n	"modulos" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"roles" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"emails" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"plantillas" : {\r\n		"index" : 1, "add": 0, "edit" : 0, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 0\r\n	},\r\n	"categorias" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"graficos" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"pages" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"reportes" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"tiendas" : {\r\n		"index" : 1, "add": 0, "edit" : 0, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 0\r\n	},\r\n	"pages" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"prospectos" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"cotizaciones" : {\r\n		"index" : 1, "add": 0, "edit" : 0, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"estadocotizaciones" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"estadoprospectos" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"monedas" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 0\r\n	},\r\n	"origenes" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"validezfechas" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"productotiendas" : {\r\n		"index" : 1, "add": 0, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"clientes" : {\r\n		"index" : 1, "add": 0, "edit" : 0, "delete" : 0, "view" : 1, "generate" : 0, "activate" : 0\r\n	},\r\n    "paises" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	},\r\n	"orderses" : {\r\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\r\n	}\r\n}', 1, '2017-01-16 15:43:29', '2017-03-28 12:10:09'),
(1, 'Super Administrador', '{\r\n	"administradores" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1, "login" : 1, "logout": 1\r\n	},\r\n	"modulos" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"roles" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"emails" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"plantillas" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"categorias" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"toolmanias" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"graficos" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"pages" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"reportes" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"tiendas" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"pages" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"prospectos" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"cotizaciones" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"estadocotizaciones" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"estadoprospectos" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"monedas" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"origenes" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"validezfechas" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"productotiendas" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"clientes" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n    "paises" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	},\r\n	"orderses" : {\r\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\r\n	}\r\n}', 1, '2017-01-16 13:04:12', '2017-03-14 17:19:08');

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;
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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT de la tabla `rp_productotiendas_prospectos`
--
ALTER TABLE `rp_productotiendas_prospectos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;
--
-- AUTO_INCREMENT de la tabla `rp_productotienda_descuentos`
--
ALTER TABLE `rp_productotienda_descuentos`
  MODIFY `id_productotienda_descuento` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `rp_prospectos`
--
ALTER TABLE `rp_prospectos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
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
