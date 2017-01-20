-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-01-2017 a las 18:00:03
-- Versión del servidor: 5.7.14
-- Versión de PHP: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema`
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

--
-- Volcado de datos para la tabla `rp_administradores`
--

INSERT INTO `rp_administradores` (`id`, `nombre`, `email`, `clave`, `google_id`, `google_dominio`, `google_nombre`, `google_apellido`, `google_imagen`, `activo`, `last_login`, `created`, `modified`, `rol_id`) VALUES
(1, 'Desarrollo Nodriza Spa', 'desarrollo@nodriza.cl', 'bfedaba41a6fc8d4997fd693b5fbc1cd999e5c0e', NULL, NULL, NULL, NULL, NULL, 1, NULL, '2017-01-16 12:30:58', '2017-01-16 13:31:29', 1),
(2, 'Vendedor test', 'vendedor1@nodriza.cl', '9c2af365a05a15d1e197208c5b22d9e25e7fd12d', NULL, NULL, NULL, NULL, NULL, 1, NULL, '2017-01-17 10:07:20', '2017-01-17 10:07:20', 2);

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

--
-- Volcado de datos para la tabla `rp_graficos`
--

INSERT INTO `rp_graficos` (`id`, `nombre`, `tipo_grafico`, `slug`, `descipcion`, `activo`, `created`, `modified`) VALUES
(1, 'Total ventas del mes', 5, 'total_ventas_del_mes', 'Select \r\nCONCAT(\'$\' , FORMAT(ROUND(SUM(Orden.total_paid_real)), 0, \'de_DE\') ) As TotalVentas, \r\nDATE_FORMAT([*START_DATE*], \'%d/%m/%Y %H:%i:%s\') AS \'InicioPeriodo\', \r\nDATE_FORMAT(DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND),  \'%d/%m/%Y %H:%i:%s\') AS \'FinPeriodo\' \r\nFROM tm_orders AS Orden\r\nWHERE\r\nOrden.date_add >= [*START_DATE*] AND Orden.date_add <= DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND ) \r\nAND (\r\nOrden.current_state = \'2\' \r\nOR Orden.current_state = \'3\'  \r\nOR Orden.current_state = \'4\' \r\nOR Orden.current_state = \'5\' )', 1, '2017-01-17 12:57:20', '2017-01-18 16:44:04'),
(4, 'Categorías del mes', 4, 'categorias_del_mes', 'SELECT\r\n     IdiomaCategoria.name AS Nombre,\r\n     SUM(CarroProducto.quantity) AS Cantidad,\r\n     CategoriaProducto.id_category AS Id\r\nFROM tm_cart AS Carro\r\nINNER JOIN\r\n      tm_orders AS Orden\r\n      ON \r\n      Orden.id_cart = Carro.id_cart\r\n      AND\r\n      Orden.date_add >= [*START_DATE*]\r\n      AND\r\n      Orden.date_add <= [*FINISH_DATE*]\r\n      AND\r\n      ( Orden.current_state = \'2\' \r\n      Or Orden.current_state = \'3\' \r\n      Or Orden.current_state = \'4\'\r\n      Or Orden.current_state = \'5\' )\r\nINNER JOIN \r\n	tm_cart_product AS CarroProducto\r\n	ON CarroProducto.id_cart = Carro.id_cart\r\nINNER JOIN\r\n	tm_product AS Producto\r\n	ON Producto.id_product = CarroProducto.id_product\r\nINNER JOIN tm_category_product AS CategoriaProducto\r\n	ON CategoriaProducto.id_product = Producto.id_product\r\nINNER JOIN tm_category AS Categoria\r\n	ON Categoria.id_category = CategoriaProducto.id_category\r\nLEFT JOIN\r\n      tm_category_lang AS IdiomaCategoria\r\n      ON IdiomaCategoria.id_category = Categoria.id_category\r\n\r\nGROUP BY Categoria.id_category\r\nORDER BY Cantidad DESC', 1, '2017-01-18 10:09:43', '2017-01-20 12:41:54'),
(5, 'Comparador de periodos', 2, 'comparador_de_periodos', 'Select ROUND(SUM(Orden.total_paid_real)) As VentaPeriodoActual, \r\n       Concat(\'$\', Format(ROUND(SUM(Orden.total_paid_real)), 0, \'de_DE\')) As Monto,  \r\n       DATE_FORMAT(Orden.date_add, \'%Y-%m\') AS Mes,\r\n       DATE_ADD([*START_DATE*], INTERVAL -1 MONTH) AS PeriodoAnteriorInicio,\r\n       DATE_ADD(DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND ), INTERVAL -1 MONTH) AS PeriodoAnteriorFinal  \r\nFrom tm_orders as Orden\r\nWhere (  \r\n      Orden.date_add >= DATE_ADD([*START_DATE*], INTERVAL -1 MONTH)\r\n      And Orden.date_add <= DATE_ADD(DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND ), INTERVAL -1 MONTH)\r\n      AND (Orden.current_state = \'2\' \r\n      Or Orden.current_state = \'3\' \r\n      Or Orden.current_state = \'4\'\r\n      Or Orden.current_state = \'5\')\r\n)\r\nOR (\r\n   Orden.date_add <= DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND )  \r\n   And Orden.date_add >= [*START_DATE*]\r\n   AND (Orden.current_state = \'2\' \r\n      Or Orden.current_state = \'3\' \r\n      Or Orden.current_state = \'4\'\r\n      Or Orden.current_state = \'5\')\r\n)\r\nGROUP BY MONTH(Orden.date_add)\r\nORDER BY Orden.date_add ASC', 1, '2017-01-18 15:46:12', '2017-01-20 11:26:01'),
(6, 'Productos del periodo', 2, 'productos_del_periodo', 'SELECT\r\n     Producto.reference AS Referencia,\r\n     SUM(CarroProducto.quantity) AS Cantidad\r\nFROM tm_cart AS Carro\r\nINNER JOIN\r\n      tm_orders AS Orden\r\n      ON \r\n      Orden.id_cart = Carro.id_cart\r\n      AND\r\n      Orden.date_add >= [*START_DATE*]\r\n      AND\r\n      Orden.date_add <= DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND )\r\n      AND\r\n      ( Orden.current_state = \'2\' \r\n      Or Orden.current_state = \'3\' \r\n      Or Orden.current_state = \'4\'\r\n      Or Orden.current_state = \'5\' )\r\nINNER JOIN \r\n	tm_cart_product AS CarroProducto\r\n	ON CarroProducto.id_cart = Carro.id_cart\r\nINNER JOIN\r\n	tm_product AS Producto\r\n	ON Producto.id_product = CarroProducto.id_product\r\n        AND Producto.active = 1\r\nGROUP BY Producto.id_product\r\nORDER BY Cantidad DESC', 1, '2017-01-18 16:06:07', '2017-01-20 12:59:18'),
(7, 'Total descuentos del periodo', 5, 'total_descuentos_del_periodo', 'Select \r\n       CONCAT(\'$\' , Format(ROUND(Sum(Orden.total_discounts_tax_incl)), 0, \'de_DE\') ) As TotalDescuentos, \r\n       [*START_DATE*] As \'InicioPeriodo\', \r\n       DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND ) As \'FinPeriodo\' \r\nFrom \r\n       tm_orders AS Orden \r\nWhere ( Orden.current_state = \'2\' \r\n      Or Orden.current_state = \'3\' \r\n      Or Orden.current_state = \'4\'\r\n      Or Orden.current_state = \'5\' )\r\n      AND Orden.date_add >= [*START_DATE*] \r\n      AND Orden.date_add <= DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND )', 1, '2017-01-19 16:24:57', '2017-01-19 16:26:10'),
(8, 'Pedidos del mes', 5, 'pedidos_del_mes', 'Select \r\n       Count(Orden.id_order) As TotalPedidos,\r\n       MONTHNAME(Orden.date_add) AS Mes,\r\n      (Select \r\n       Count(tm_orders.id_order) As TotalPedidos\r\nFrom tm_orders \r\nWhere ( tm_orders.current_state = \'2\' \r\n      Or tm_orders.current_state = \'3\' \r\n      Or tm_orders.current_state = \'4\'\r\n      Or tm_orders.current_state = \'5\' )\r\n      AND tm_orders.date_add >= [*START_DATE*] \r\n      AND tm_orders.date_add <= DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND )) AS Total\r\nFrom tm_orders AS Orden \r\nWhere (  \r\n      Orden.date_add >= DATE_ADD([*START_DATE*], INTERVAL -1 MONTH)\r\n      And Orden.date_add <= DATE_ADD(DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND ), INTERVAL -1 MONTH)\r\n      AND (Orden.current_state = \'2\' \r\n      Or Orden.current_state = \'3\' \r\n      Or Orden.current_state = \'4\'\r\n      Or Orden.current_state = \'5\')\r\n)\r\nOR (\r\n   Orden.date_add <= DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND )  \r\n   And Orden.date_add >= [*START_DATE*]\r\n   AND (Orden.current_state = \'2\' \r\n      Or Orden.current_state = \'3\' \r\n      Or Orden.current_state = \'4\'\r\n      Or Orden.current_state = \'5\')\r\n)\r\nGroup by month(Orden.date_add) \r\nOrder By Orden.date_add DESC', 1, '2017-01-19 16:29:01', '2017-01-20 10:03:40'),
(9, 'Compradores del periodo', 4, 'compradores_del_periodo', 'Select \r\n       ROUND(SUM(Orden.total_paid_real)) As pagado, \r\n       CONCAT(Cliente.firstname, \' \', Cliente.lastname) AS nombre   \r\nFrom \r\n       tm_customer AS Cliente\r\nLeft join tm_orders AS Orden\r\n     On Orden.id_customer = Cliente.id_customer \r\nWhere ( Orden.current_state = \'2\' \r\n     Or Orden.current_state = \'3\' \r\n     Or Orden.current_state = \'4\'\r\n     Or Orden.current_state = \'5\' )\r\n     AND Orden.date_add >= [*START_DATE*]  \r\n     AND Orden.date_add <= DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND )\r\ngroup by Cliente.id_customer\r\nOrder by ROUND(SUM(Orden.total_paid_real)) DESC\r\nLimit 10;', 1, '2017-01-19 16:59:16', '2017-01-19 16:59:16'),
(10, 'Marcas del periodo', 1, 'marcas_del_periodo', 'SELECT\r\n     Producto.reference AS Referencia,\r\n     Proveedor.name AS Nombre,\r\n     SUM(CarroProducto.quantity) AS Cantidad\r\nFROM tm_cart AS Carro \r\nINNER JOIN\r\n      tm_orders AS Orden\r\n      ON \r\n      Orden.id_cart = Carro.id_cart\r\n      AND\r\n      Orden.date_add >= [*START_DATE*]\r\n      AND\r\n      Orden.date_add <= DATE_ADD([*FINISH_DATE*], INTERVAL 86399 SECOND )\r\n      AND\r\n      ( Orden.current_state = \'2\' \r\n      Or Orden.current_state = \'3\' \r\n      Or Orden.current_state = \'4\'\r\n      Or Orden.current_state = \'5\' )\r\nINNER JOIN \r\n  tm_cart_product AS CarroProducto\r\n  ON CarroProducto.id_cart = Carro.id_cart\r\nINNER JOIN\r\n  tm_product AS Producto\r\n  ON Producto.id_product = CarroProducto.id_product\r\nINNER JOIN tm_product_supplier AS ProductoProveedor\r\n  ON ProductoProveedor.id_product = Producto.id_product\r\nINNER JOIN tm_supplier AS Proveedor\r\n  ON Proveedor.id_supplier = ProductoProveedor.id_supplier\r\nGROUP BY Proveedor.id_supplier\r\nORDER BY Cantidad DESC\r\nLIMIT 20', 1, '2017-01-20 13:43:02', '2017-01-20 13:43:02');

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
  `icono` varchar(20) DEFAULT NULL,
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
(3, NULL, 'Newsletter', '', 'fa fa-envelope', 1, '2017-01-16 15:31:55', '2017-01-17 10:53:12'),
(4, 3, 'Newsletter', 'emails', 'fa fa-envelope-o', 1, '2017-01-16 15:32:39', '2017-01-16 15:32:39'),
(5, 3, 'Plantillas', 'plantillas', 'fa fa-file-text-o', 1, '2017-01-16 15:34:57', '2017-01-17 10:53:32'),
(6, 1, 'Administradores', 'administradores', 'fa fa-user', 1, '2017-01-16 15:39:07', '2017-01-16 15:39:07'),
(7, 1, 'Roles de usuario', 'roles', 'fa fa-flag-checkered', 1, '2017-01-16 15:40:45', '2017-01-16 15:40:45'),
(8, 3, 'Productos', 'toolmanias', 'fa fa-shopping-bag', 1, '2017-01-16 16:56:57', '2017-01-16 16:56:57'),
(9, 3, 'Categorías', 'categorias', 'fa fa-list-alt', 1, '2017-01-16 16:58:43', '2017-01-16 16:58:43'),
(10, NULL, 'Ventas', '', 'fa fa-money', 1, '2017-01-17 10:54:12', '2017-01-17 10:54:12'),
(11, 10, 'Reportes', 'reportes', 'fa fa-file-text', 1, '2017-01-17 10:55:01', '2017-01-17 10:55:01'),
(12, 10, 'Gráficos', 'graficos', 'fa fa-bar-chart-o', 1, '2017-01-17 10:55:35', '2017-01-17 10:55:35'),
(13, 10, 'Tiendas', 'tiendas', 'fa fa-globe', 1, '2017-01-17 10:56:28', '2017-01-17 12:28:04');

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
(4, 1, 4),
(22, 1, 10),
(6, 1, 6),
(7, 1, 7),
(23, 2, 10),
(12, 1, 8),
(13, 1, 9),
(20, 1, 3),
(21, 1, 5),
(24, 1, 11),
(25, 2, 11),
(26, 1, 12),
(28, 1, 13),
(29, 2, 13);

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

--
-- Volcado de datos para la tabla `rp_reportes`
--

INSERT INTO `rp_reportes` (`id`, `nombre`, `activo`, `created`, `modified`, `tienda_id`) VALUES
(1, 'Informe de ventas Toolmanía', 1, '2017-01-17 12:25:36', '2017-01-17 12:31:18', 1);

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
(1, 'Super Administrador', '{\n	"administradores" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1, "login" : 1, "logout": 1\n	},\n	"modulos" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\n	},\n	"roles" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\n	},\n	"emails" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\n	},\n	"plantillas" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\n	},\n	"categorias" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\n	},\n	"toolmanias" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\n	},\n	"graficos" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\n	},\n	"pages" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\n	},\n	"reportes" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\n	},\n	"tiendas" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\n	},\n	"pages" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 1, "view" : 1, "generate" : 1, "activate" : 1\n	}\n}', 1, '2017-01-16 13:04:12', '2017-01-16 18:03:30'),
(2, 'Ventas', '{\n	"administradores" : {\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0, "login" : 0, "logout": 0\n	},\n	"modulos" : {\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\n	},\n	"roles" : {\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\n	},\n	"emails" : {\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\n	},\n	"plantillas" : {\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\n	},\n	"categorias" : {\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\n	},\n	"toolmanias" : {\n		"index" : 0, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\n	},\n	"graficos" : {\n		"index" : 1, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\n	},\n	"pages" : {\n		"index" : 1, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\n	},\n	"reportes" : {\n		"index" : 1, "add": 1, "edit" : 1, "delete" : 0, "view" : 1, "generate" : 1, "activate" : 1\n	},\n	"tiendas" : {\n		"index" : 1, "add": 0, "edit" : 0, "delete" : 0, "view" : 1, "generate" : 0, "activate" : 1\n	},\n	"pages" : {\n		"index" : 1, "add": 0, "edit" : 0, "delete" : 0, "view" : 0, "generate" : 0, "activate" : 0\n	}\n}', 1, '2017-01-16 15:43:29', '2017-01-17 10:52:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rp_tiendas`
--

CREATE TABLE `rp_tiendas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `url` varchar(100) NOT NULL,
  `configuracion` varchar(50) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `rp_tiendas`
--

INSERT INTO `rp_tiendas` (`id`, `nombre`, `url`, `configuracion`, `activo`, `created`, `modified`) VALUES
(1, 'Toolmanía', 'www.toolmania.cl', 'toolmania', 1, '2017-01-17 12:29:20', '2017-01-17 12:31:08');

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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `rp_administradores`
--
ALTER TABLE `rp_administradores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `rp_graficos`
--
ALTER TABLE `rp_graficos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT de la tabla `rp_graficos_reportes`
--
ALTER TABLE `rp_graficos_reportes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `rp_logs`
--
ALTER TABLE `rp_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `rp_modulos`
--
ALTER TABLE `rp_modulos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT de la tabla `rp_modulos_roles`
--
ALTER TABLE `rp_modulos_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT de la tabla `rp_reportes`
--
ALTER TABLE `rp_reportes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `rp_roles`
--
ALTER TABLE `rp_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `rp_tiendas`
--
ALTER TABLE `rp_tiendas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
