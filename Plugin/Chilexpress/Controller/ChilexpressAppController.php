<?php

/**
 * Plugin para Cakephp 2.X.
 * Éste plugin integra todos los servicios Web que Chilexpress pone a disposición de sus clientes.
 * 
 * Servicios Webs integrados:
 * 	Tracking Push: 
 * 		Seguimiento de OT
 * 		
 * 	Geolocalización: 
 * 		Servicio web utilizaod para normalizar direcciones y consultar coverturas de Chilexpress en el territorio nacional.
 * 		
 * 	Tarificación: 
 * 		Servicio web que permite calcular el valor del despacho Chilexpress de un productos según su peso y volumen.
 *
 *	Generación de OT
 *		Servicio web utilizado para generar un Orden de transporte validada en Chilexpress. 
 *
 * 	Impresión de Etiqueta
 * 		Retorna la etiqueta de una OT en específica. Ésta etiqueta se puede guardar en formato imagen o imprimirla 
 * 		directamete en una impersora compatible.
 *
 * 	@author 		Cristian A. Rojas Pérez <cristian.rojas@nodriza.cl>
 * 	@since 			01-2018 	
 * 	@version 		1.0
 */

class ChilexpressAppController extends AppController
{
}
