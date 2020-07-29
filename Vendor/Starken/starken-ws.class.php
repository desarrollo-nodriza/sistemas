<?php
/**
 * StarkenWebServices class
 *
 * Librería para integración de Starken Web Services.
 *
 * @author		Nodriza Spa (http://nodriza.cl/)
 * @copyright	Copyright (c) Nodriza Spa (http://nodriza.cl/)
 * @link		http://nodriza.cl/
 * @version		1.0
 */

class StarkenWebServices {

	/**
	 * Rut para la autenticación a la Api REST (cotización y seguimiento).
	 * @var string
	 */
	private $rutApiRest = '';

	/**
	 * Clave para la autenticación a la Api REST (cotización y seguimiento).
	 * @var string
	 */
	private $claveApiRest = '';

	/**
	 * Rut (sin dígito verificador) de la empresa emisora, para la autenticación a la Api SOAP (generación de orden).
	 * @var string
	 */
	private $rutEmpresaEmisora = '';

	/**
	 * Rut (sin dígito verificador) del usuario emisor, para la autenticación a la Api SOAP (generación de orden).
	 * @var string
	 */
	private $rutUsuarioEmisor = '';

	/**
	 * Clave del usuario emisor, para la autenticación a la Api SOAP (generación de orden).
	 * @var string
	 */
	private $claveUsuarioEmisor = '';

	/**
	 * Número de la cuenta corriente (se usa cuando el tipo de pago es "2", pago con Cta. Cte.).
	 * @var string
	 */
	private $numeroCtaCte = '';

	/**
	 * Digito Verificador del número de la cuenta corriente (se usa cuando el tipo de pago es "2", pago con Cta. Cte.).
	 * @var string
	 */
	private $dvNumeroCtaCte = '';

	/**
	 * Centro de costo de la cuenta corriente (se usa cuando el tipo de pago es "2").
	 * @var string
	 */
	private $centroCostoCtaCte = '';

	/**
	 * Url Api REST (se carga desde el archivo de configuraciones).
	 * @var string
	 */
	private $urlApiRest = '';

	/**
	 * Url Api SOAP (se carga desde el archivo de configuraciones).
	 * @var string
	 */
	private $urlApiSoap = '';

	/**
	 * Tiempo máximo de espera por las respuestas de los servicios de Starken.
	 * @var string
	 */
	private $timeOut = 0;

	/**
	 * Listado de métodos disponibles en la Api REST.
	 * @var array
	 */
	private $metodosRestDisponibles = array();

	/**
	 * Listado de métodos con datos requeridos en el request en la Api REST.
	 * @var array
	 */
	private $metodosRestConRequest = array();

	/**
	 * Lista de campos requeridos para cada request de la Api REST.
	 * @var array
	 */
	private $obligatoriosXRequest = array();

	/**
	 * Lista de campos requeridos para cada request de la Api SOAP.
	 * @var array
	 */
	private $obligatoriosSoap = array();

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * Constructor
	 * @param string $rutEmpresaEmisora Rut Empresa Emisora.
	 * @param string $rutUsuarioEmisor Rut usuario Emisor.
	 * @param string $claveUsuarioEmisor Clave Usuario Emisor.
	 * @param string $numeroCtaCte Número Cta Cte.
	 * @param string $dvNumeroCtaCte Dv Número Cta Cte.
	 * @param string $centroCostoCtaCte Centro Costo Cta Cte.
	 */
	public function __construct($rutApiRest = null, $claveApiRest = null, $rutEmpresaEmisora = null, $rutUsuarioEmisor = null, $claveUsuarioEmisor = null, $numeroCtaCte = null, $dvNumeroCtaCte = null, $centroCostoCtaCte = null) {

		//Se inicializan las propiedades de acuerdo a los parámetros.
		$this->rutApiRest = $rutApiRest;
		$this->claveApiRest = $claveApiRest;
		$this->rutEmpresaEmisora = $rutEmpresaEmisora;
		$this->rutUsuarioEmisor = $rutUsuarioEmisor;
		$this->claveUsuarioEmisor = $claveUsuarioEmisor;
		$this->numeroCtaCte = $numeroCtaCte;
		$this->dvNumeroCtaCte = $dvNumeroCtaCte;
		$this->centroCostoCtaCte = $centroCostoCtaCte;

		//Se cargan las configuraciones.
		$configuraciones = parse_ini_file(__DIR__ . '/config/config.properties');
		$this->urlApiRest = $configuraciones['urlApiRest'];
		$this->urlApiSoap = $configuraciones['urlApiSoap'];
		$this->timeOut = $configuraciones['timeOut'];
		$this->metodosRestDisponibles = explode(',', $configuraciones['metodosRestDisponibles']);
		$this->metodosRestConRequest = explode(',', $configuraciones['metodosRestConRequest']);
		$this->obligatoriosSoap = explode(',', $configuraciones['obligatoriosSoap']);

		//Se cargan los campos obligatorios por request a la Api REST.
		$this->obligatoriosXRequest = parse_ini_file(__DIR__ . '/config/obligatorios.properties');

	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * tryRequest
	 * Realiza un request a la Api rest.
	 * @param string $method Método de la Api a ejecutar.
	 * @param string JSON $request Parámetros de entrada para el request.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @param bool $auth  Indica si el servicio necesita o no autenticación.
	 * @return string JSON Response o error junto con su descripción.
	 */
	private function tryRequest ($metodo = '', $request = '', $testing = false, $auth = true) {
		
		//Se valida que se haya recibido el método.
		if (empty($metodo)) {

			$response = array(
				'code' => 'error',
				'body' => 'El Método es requerido.'
			);

			return json_encode($response);

		}

		//Se valida que el método sea válido.
		if (!in_array($metodo, $this->metodosRestDisponibles)) {

			$response = array(
				'code' => 'error',
				'body' => 'El Método no es válido.'
			);

			return json_encode($response);

		}

		$headers = array(
			"Content-Type: application/json; charset=utf-8"
		);

		//Se validan los métodos con parámetros obligatorios.
		if (in_array($metodo, $this->metodosRestConRequest)) {

			//El request es obligatorio.
			if (empty($request)) {

				$response = array(
					'error' => true,
					'description' => 'El Request es requerido para el método ' .$metodo. '.'
				);
				
				return json_encode($response);

			}

			//Se valida el request.
			$requestValido = $this->validarRequestRest($metodo, $request);

			if ($requestValido !== true) {

				$response = array(
					'error' => true,
					'description' => $requestValido
				);
				
				return json_encode($response);

			}

			$headers[] = "Content-Length: " . strlen($request);

		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->urlApiRest . $metodo);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//Si el request se realiza en producción, se usan los datos de autenticación.
		if (!$testing && $auth) {
			$headers[] = 'Rut: ' . $this->rutApiRest;
			$headers[] = 'Clave: ' . $this->claveApiRest;
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		//Si van parámetros en el request.
		if ($request) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		}
		
		$result = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		//Si ocurrió un error en la ejecución de WS.
		if ($http_code !== 200) {

			$response = array(
				'code' => 'error',
				'body' => 'Error en la ejecución del WS.'
			);
			
			return json_encode($response);

		}

		//Ejecución correcta.
		$response = $this->formatearResult($metodo, $result);
		return json_encode($response);

	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * validarRequestRest
	 * Valida los datos obligatorios de un request, según el método.
	 * @param string $metodo Método a ejecutar.
	 * @param string $request Respuesta a formatear.
	 * @return mixed true si el request es correcto o un string con el mensaje de error si corresponde.
	 */
	private function validarRequestRest ($metodo = '', $request = '') {

		$dataRequest = json_decode($request, true);

		//Se validan los parámetros obligatorios.
		$obligatorios = explode(',', $this->obligatoriosXRequest[$metodo . 'Obligatorios']);
		
		foreach ($obligatorios as $obligatorio) {

			if (!isset($dataRequest[$obligatorio])) {
				return "Error: Falta el parámetro '" .$obligatorio. "'.";
			}

			if (empty($dataRequest[$obligatorio])) {
				return "Error: El parámetro '" .$obligatorio. "' está vacío.";
			}

			//Si el parámetro obligatorio es un listado.
			if (isset($this->obligatoriosXRequest[$metodo . 'ObligatoriosSub'])) {

				$obligatoriosSub = explode(',', $this->obligatoriosXRequest[$metodo . 'ObligatoriosSub']);

				foreach ($obligatoriosSub as $obligatorioSub) {

					$encontrado = array();

					foreach ($dataRequest[$obligatorio] as $datosSub) {

						if (array_key_exists($obligatorioSub, $datosSub)) {
							$encontrado = $datosSub;
							break;
						}

					}

					if (empty($encontrado)) {
						return "Error: Falta, o está vacío, el parámetro '" .$obligatorioSub. "'.";
					}

				}

			}

		}

		return true;

	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * formatearResult
	 * Formatea un response según el método.
	 * @param string $metodo Método a ejecutar.
	 * @param string $result Respuesta a formatear.
	 * @return array Arreglo con el response formateado.
	 */
	private function formatearResult ($metodo = '', $result = '') {

		$dataResult = json_decode($result, true);

		switch ($metodo) {

			//----------------------------------------------------------------------------------------------------
			case 'getDetalleSeguimiento':
				return array(
					'code' => 'success',
					'body' => $dataResult
				);
			break;

			//----------------------------------------------------------------------------------------------------
			case 'getSeguimiento':
				return array(
					'code' => 'success',
					'body' => $dataResult
				);
			break;

			//----------------------------------------------------------------------------------------------------
			case 'consultarLinkImagen':

				//Respuesta correcta.
				if ($dataResult['codigoRespuesta'] == 1) {
					return array(
						'code' => 'success',
						'body' => $dataResult['linkImagen']
					);
				}

				//Error.

				//Si hay mensaje de error.
				if (isset($dataResult['descripcionRespuesta'])) {
					return array(
						'code' => 'error',
						'body' => $dataResult['descripcionRespuesta']
					);
				}

				return array(
					'code' => 'error',
					'body' => 'Error en la ejecución del WS.'
				);

			break;

			//----------------------------------------------------------------------------------------------------
			case 'getSeguimientoNuevo':
				return array(
					'code' => 'success',
					'body' => $dataResult
				);
			break;

			//----------------------------------------------------------------------------------------------------
			case 'getEstadoReclamo':

				//Respuesta correcta.
				if ($dataResult['codigoRespuesta'] == 1) {
					return array(
						'code' => 'success',
						'body' => $dataResult
					);
				}

				//Error.
				
				//Si hay mensaje de error.
				if (isset($dataResult['mensajeRespuesta'])) {
					return array(
						'code' => 'error',
						'body' => $dataResult['mensajeRespuesta']
					);
				}
				
				//Sin mensaje de error.
				return array(
					'code' => 'error',
					'body' => 'Error en la ejecución del WS.'
				);

			break;

			//----------------------------------------------------------------------------------------------------
			case 'getSeguimientoEmpresa':
				return array(
					'code' => 'success',
					'body' => $dataResult
				);
			break;

			//----------------------------------------------------------------------------------------------------
			case 'listarCiudadesOrigen':
				return array(
					'code' => 'success',
					'body' => $dataResult['listaCiudadesOrigen']
				);
			break;

			//----------------------------------------------------------------------------------------------------
			case 'listarCiudadesDestino':
				return array(
					'code' => 'success',
					'body' => $dataResult['listaCiudadesDestino']
				);
			break;

			//----------------------------------------------------------------------------------------------------
			case 'consultarCobertura':

				//Respuesta correcta.
				if ($dataResult['codigoRespuesta'] == 1) {
					return array(
						'code' => 'success',
						'body' => $dataResult['listaTarifas']
					);
				}

				//Error.
				
				//Si hay mensaje de error.
				if (isset($dataResult['mensajeRespuesta'])) {
					return array(
						'code' => 'error',
						'body' => $dataResult['mensajeRespuesta']
					);
				}
				
				//Sin mensaje de error.
				return array(
					'code' => 'error',
					'body' => 'Error en la ejecución del WS.'
				);

			break;

			//----------------------------------------------------------------------------------------------------
			case 'consultarCoberturaBultoSobre':

				//Respuesta correcta.
				if ($dataResult['codigoRespuesta'] == 1) {
					return array(
						'code' => 'success',
						'body' => $dataResult['listaTarifas']
					);
				}

				//Error.
				
				//Si hay mensaje de error.
				if (isset($dataResult['mensajeRespuesta'])) {
					return array(
						'code' => 'error',
						'body' => $dataResult['mensajeRespuesta']
					);
				}
				
				//Sin mensaje de error.
				return array(
					'code' => 'error',
					'body' => 'Error en la ejecución del WS.'
				);

			break;

			//----------------------------------------------------------------------------------------------------
			case 'getDetalleSeguimientoNuevo':
				return array(
					'code' => 'success',
					'body' => $dataResult
				);
			break;

		}

	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * getDetalleSeguimiento
	 * Permite realizar el seguimiento de envíos ingresando una lista de órdenes de flete.
	 * @param string JSON $request Parámetros de entrada para el request.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @return string JSON response o error junto con su descripción.
	 */
	public function getDetalleSeguimiento ($request = '', $testing = false) {
		return $this->tryRequest('getDetalleSeguimiento', $request, $testing);
	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * getSeguimiento
	 * Permite consultar el seguimiento de una lista de órdenes de flete.
	 * @param string JSON $request Parámetros de entrada para el request.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @return string JSON response o error junto con su descripción.
	 */
	public function getSeguimiento ($request = '', $testing = false) {
		return $this->tryRequest('getSeguimiento', $request, $testing);
	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * consultarLinkImagen
	 * Obtiene el enlace de una imagen asociada a una orden de flete.
	 * @param string JSON $request Parámetros de entrada para el request.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @return string JSON response o error junto con su descripción.
	 */
	public function consultarLinkImagen ($request = '', $testing = false) {

		if (!$testing) {

			$datosRequest = json_decode($request, true);

			$datosRequest['rutEmpresa'] = $this->rutEmpresaEmisora;
			$datosRequest['rutUsuario'] = $this->rutUsuarioEmisor;
			$datosRequest['password'] = $this->claveUsuarioEmisor;

			$request = json_encode($dataRequest);

		}

		return $this->tryRequest('consultarLinkImagen', $request, $testing);

	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * getSeguimientoNuevo
	 * Permite obtener el seguimiento de un envío consultando por el número de orden de flete.
	 * @param string JSON $request Parámetros de entrada para el request.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @return string JSON response o error junto con su descripción.
	 */
	public function getSeguimientoNuevo ($request = '', $testing = false) {
		return $this->tryRequest('getSeguimientoNuevo', $request, $testing);
	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * getEstadoReclamo
	 * Permite consultar reclamos asociados a envíos.
	 * @param string JSON $request Parámetros de entrada para el request.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @return string JSON response o error junto con su descripción.
	 */
	public function getEstadoReclamo ($request = '', $testing = false) {
		return $this->tryRequest('getEstadoReclamo', $request, $testing);
	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * getSeguimientoEmpresa
	 * Permite realizar el seguimiento de un envío ingresando el número de documento del cliente.
	 * @param string JSON $request Parámetros de entrada para el request.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @return string JSON response o error junto con su descripción.
	 */
	public function getSeguimientoEmpresa ($request = '', $testing = false) {

		if (!$testing) {

			$datosRequest = json_decode($request, true);

			if (isset($datosRequest['listaSeguimientos'])) {
				if (!empty($datosRequest['listaSeguimientos'])) {
					
					for ($i = 0; $i < count($datosRequest['listaSeguimientos']); $i++) {
						$datosRequest['listaSeguimientos'][$i]['ctaCteCliente'] = $this->numeroCtaCte;
					}

				}
			}

			$request = json_encode($dataRequest);

		}

		return $this->tryRequest('getSeguimientoEmpresa', $request, $testing);

	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * listarCiudadesOrigen
	 * Obtiene información de todas las ciudades de origen de los envíos.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @return string JSON response o error junto con su descripción.
	 */
	public function listarCiudadesOrigen ($testing = false, $auth = false) {
		return $this->tryRequest('listarCiudadesOrigen', '', $testing, $auth);
	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * listarCiudadesDestino
	 * Obtiene información de todas las ciudades de destino de los envíos.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @return string JSON response o error junto con su descripción.
	 */
	public function listarCiudadesDestino ($testing = false, $auth = false) {
		return $this->tryRequest('listarCiudadesDestino', '', $testing, $auth);
	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * consultarCobertura
	 * Permite conocer los costos asociados a un envío.
	 * @param string JSON $request Parámetros de entrada para el request.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @return string JSON response o error junto con su descripción.
	 */
	public function consultarCobertura ($request = '', $testing = false) {
		return $this->tryRequest('consultarCobertura', $request, $testing);
	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * consultarCoberturaBultoSobre
	 * Permite conocer los costos asociados a un envío por tipo de bulto.
	 * @param string JSON $request Parámetros de entrada para el request.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @return string JSON response o error junto con su descripción.
	 */
	public function consultarCoberturaBultoSobre ($request = '', $testing = false) {
		return $this->tryRequest('consultarCoberturaBultoSobre', $request, $testing);
	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * getDetalleSeguimientoNuevo
	 * Permite conocer los costos asociados a un envío.
	 * @param string JSON $request Parámetros de entrada para el request.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @return string JSON response o error junto con su descripción.
	 */
	public function getDetalleSeguimientoNuevo ($request = '', $testing = false) {
		return $this->tryRequest('getDetalleSeguimientoNuevo', $request, $testing);
	}

	/**
	 * ----------------------------------------------------------------------------------------------------
	 * generarOrden
	 * Permite generar una orden de flete.
	 * @param string JSON $request Parámetros de entrada para el request.
	 * @param bool $testing Se indica si se está ejecutando como testing.
	 * @return string JSON response o error junto con su descripción.
	 */
	public function generarOrden ($request = '', $testing = false) {

		$dataRequest = json_decode($request, true);
		
		//----------------------------------------------------------------------------------------------------
		//Datos obligatorios que deben venir del constructor. (Para testing no se incluyem, vienen de la data de prueba.)
		if (!$testing) {

			if ($this->rutEmpresaEmisora == '') {
				$respuesta = array(
					'code' => 'error',
					'body' => 'El parámetro rutEmpresaEmisora está vacío.'
				);
				return json_encode($respuesta);
			}

			$dataRequest['rutEmpresaEmisora'] = $this->rutEmpresaEmisora;

			if ($this->rutUsuarioEmisor == '') {
				$respuesta = array(
					'code' => 'error',
					'body' => 'El parámetro rutUsuarioEmisor está vacío.'
				);
				return json_encode($respuesta);
			}

			$dataRequest['rutUsuarioEmisor'] = $this->rutUsuarioEmisor;

			if ($this->claveUsuarioEmisor == '') {
				$respuesta = array(
					'code' => 'error',
					'body' => 'El parámetro claveUsuarioEmisor está vacío.'
				);
				return json_encode($respuesta);
			}

			$dataRequest['claveUsuarioEmisor'] = $this->claveUsuarioEmisor;

		}

		//----------------------------------------------------------------------------------------------------
		//Obligatorios del request.
		foreach ($this->obligatoriosSoap as $obligatorio) {

			if (!isset($dataRequest[$obligatorio])) {
				$respuesta = array(
					'code' => 'error',
					'body' => 'Falta el parámetro ' .$obligatorio. '.'
				);
				return json_encode($respuesta);
			}
			
			if ($dataRequest[$obligatorio] == '') {
				$respuesta = array(
					'code' => 'error',
					'body' => 'El parámetro ' .$obligatorio. ' está vacío.'
				);
				return json_encode($respuesta);
			}

		}

		//----------------------------------------------------------------------------------------------------
		//Datos de cuenta corriente. Se deben agregar cuando tipoPago es 2.
		$obligatorios = array('numeroCtaCte', 'dvNumeroCtaCte', 'centroCostoCtaCte');

		if ($dataRequest['tipoPago'] == 2) {
			
			foreach ($obligatorios as $obligatorio) {

				if (!isset($dataRequest[$obligatorio])) {
					$respuesta = array(
						'code' => 'error',
						'body' => 'Falta el parámetro ' .$obligatorio. '.'
					);
					return json_encode($respuesta);
				}
				
				if ($dataRequest[$obligatorio] == '') {
					$respuesta = array(
						'code' => 'error',
						'body' => 'El parámetro ' .$obligatorio. ' está vacío.'
					);
					return json_encode($respuesta);
				}
				
			}
			
		}

		//Para prevenir que vayan datos sin cumplir con la condición anterior.
		else {
			foreach ($obligatorios as $obligatorio) {
				#unset($dataRequest[$obligatorio]);
			}
		}

		//----------------------------------------------------------------------------------------------------
		//Si se ingresa rutDestinatario, también se deben comprobar los campos demás campos de datos del usuario.
		$existeRut = true;
		$obligatorios = array('rutDestinatario', 'dvRutDestinatario', 'apellidoPaternoDestinatario', 'apellidoMaternoDestinatario');

		if (isset($dataRequest['rutDestinatario'])) {

			if ($dataRequest['rutDestinatario'] != '') {

				foreach ($obligatorios as $obligatorio) {

					if (!isset($dataRequest[$obligatorio])) {
						$respuesta = array(
							'code' => 'error',
							'body' => 'Falta el parámetro ' .$obligatorio. '.'
						);
						return json_encode($respuesta);
					}
					
					if ($dataRequest[$obligatorio] == '') {
						$respuesta = array(
							'code' => 'error',
							'body' => 'El parámetro ' .$obligatorio. ' está vacío.'
						);
						return json_encode($respuesta);
					}

				}

			}

			else {
				$existeRut = false;
			}

		}

		else {
			$existeRut = false;
		}

		if (!$existeRut) {
			foreach ($obligatorios as $obligatorio) {
				#unset($dataRequest[$obligatorio]);
			}
		}

		//----------------------------------------------------------------------------------------------------
		//Dirección de destinatario. Se deben agregar cuando tipoEntrega es 2.
		$obligatorios = array('direccionDestinatario', 'numeracionDireccionDestinatario', 'telefonoDestinatario');

		if ($dataRequest['tipoEntrega'] == 2) {
			
			foreach ($obligatorios as $obligatorio) {

				if (!isset($dataRequest[$obligatorio])) {
					$respuesta = array(
						'code' => 'error',
						'body' => 'Falta el parámetro ' .$obligatorio. '.'
					);
					json_encode($respuesta);
				}
				
				if ($dataRequest[$obligatorio] == '') {
					$respuesta = array(
						'code' => 'error',
						'body' => 'El parámetro ' .$obligatorio. ' está vacío.'
					);
					json_encode($respuesta);
				}
				
			}
			
		}

		//Para prevenir que vayan datos sin cumplir con la condición anterior.
		else {
			foreach ($obligatorios as $obligatorio) {
				#unset($dataRequest[$obligatorio]);
			}
		}

		//----------------------------------------------------------------------------------------------------
		//Listado de documentos.
		if (isset($dataRequest['documentos'])) {

			if (!empty($dataRequest['documentos'])) {

				if (count($dataRequest['documentos']) > 5) {
					$respuesta = array(
						'code' => 'error',
						'body' => 'Se deben ingresar máximo 5 documentos.'
					);
					return json_encode($respuesta);
				}

				$obligatorios = array('tipoDocumento', 'numeroDocumento', 'generaEtiquetaDocumento');
				$num = 0;

				foreach ($dataRequest['documentos'] as $documento) {

					$num++;
					$dataDocumento = array();

					foreach ($obligatorios as $obligatorio) {

						if (!isset($documento[$obligatorio])) {
							$respuesta = array(
								'code' => 'error',
								'body' => 'Falta el parámetro ' .$obligatorio. '.'
							);
							return json_encode($respuesta);
						}
						
						if ($documento[$obligatorio] == '') {
							$respuesta = array(
								'code' => 'error',
								'body' => 'El parámetro ' .$obligatorio. ' está vacío.'
							);
							return json_encode($respuesta);
						}

						$dataDocumento[$obligatorio . $num] = $documento[$obligatorio];
						
					}

					foreach ($dataDocumento as $key => $value) {
						$dataRequest[$key] = $value;
					}

				}

				$completarDocumentos = count($dataRequest['documentos']);

			}

			else {
				$completarDocumentos = 0;
			}

		}

		else {
			$completarDocumentos = 0;
		}

		unset($dataRequest['documentos']);

		//Se completan los 5 documentos.
		if ($completarDocumentos >= 0) {
			for ($i = $completarDocumentos; $i < 5; $i++) {
				$k = $i+1;
				$dataRequest['tipoDocumento' . $k] = '';
				$dataRequest['numeroDocumento' . $k] = '';
				$dataRequest['generaEtiquetaDocumento' . $k] = '';
			}
		}

		//----------------------------------------------------------------------------------------------------
		//Listado de encargos.
		if (isset($dataRequest['encargos'])) {

			if (!empty($dataRequest['encargos'])) {

				if (count($dataRequest['encargos']) > 5) {
					$respuesta = array(
						'code' => 'error',
						'body' => 'Se deben ingresar máximo 5 encargos.'
					);
					return json_encode($respuesta);
				}

				$obligatorios = array('tipoEncargo', 'cantidadEncargo');
				$num = 0;

				foreach ($dataRequest['encargos'] as $encargo) {

					$num++;
					$dataEncargo = array();

					foreach ($obligatorios as $obligatorio) {

						if (!isset($encargo[$obligatorio])) {
							$respuesta = array(
								'code' => 'error',
								'body' => 'Falta el parámetro ' .$obligatorio. '.'
							);
							return json_encode($respuesta);
						}
						
						if ($encargo[$obligatorio] == '') {
							$respuesta = array(
								'code' => 'error',
								'body' => 'El parámetro ' .$obligatorio. ' está vacío.'
							);
							return json_encode($respuesta);
						}

						$dataEncargo[$obligatorio . $num] = $encargo[$obligatorio];
						
					}

					foreach ($dataEncargo as $key => $value) {
						$dataRequest[$key] = $value;
					}
					
				}

				$completarEncargos = count($dataRequest['encargos']);

			}

			else {
				$completarEncargos = 0;
			}

		}

		else {
			$completarEncargos = 0;
		}

		unset($dataRequest['encargos']);

		//Se completan los 5 encargos.
		if ($completarEncargos >= 0) {
			for ($i = $completarEncargos; $i < 5; $i++) {

				$k = $i+1;

				$dataRequest['tipoEncargo' . $k] = '';
				$dataRequest['cantidadEncargo' . $k] = '';
			}
		}
		
		try {

			//echo "<pre>"; print_r($dataRequest); echo "</pre>"; exit;

			$options = array(
				'uri' =>'http://schemas.xmlsoap.org/soap/envelope/',
				'style' => SOAP_RPC,
				'use' => SOAP_ENCODED,
				'soap_version' => SOAP_1_1,
				'cache_wsdl'=> WSDL_CACHE_NONE,
				'connection_timeout' => 30,
				'trace' => true ,
				'encoding' => 'UTF-8',
				'exceptions' => true
			);

			$client  = new SoapClient($this->urlApiSoap);
			$result = $client->Execute(array('Param_inco_item' => $dataRequest));

			if (isset($result->Param_out_item->Encargos->EncargosItem)) {
				$result->Param_out_item->Encargos = $result->Param_out_item->Encargos->EncargosItem;
			}

			if (isset($result->Param_out_item->Otros->OtrosItem)) {
				$result->Param_out_item->Otros = $result->Param_out_item->Otros->OtrosItem;
			}

			//print_r($result->Param_out_item->Encargos->EncargosItem); exit;

			$respuesta = array(
				'code' => 'success',
				'body' => $result->Param_out_item
			);

			return json_encode($respuesta);

		}

		catch (Exception $e) {
			return json_encode(array(
				'code' => 'error',
				'body' => 'Error en la ejecución del servicio: ' . $e->getMessage()
			));
		}

	}
	
	/**
	 * ----------------------------------------------------------------------------------------------------
	 * Destructor
	 */
	public function __destruct () {
	}

}

?>