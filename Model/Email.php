<?php
App::uses('AppModel', 'Model');
class Email extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $useDbConfig = 'reportes';
	public $displayField	= 'nombre';

	/**
	 * BEHAVIORS
	 */
	var $actsAs			= array(
		/**
		 * IMAGE UPLOAD
		 */
		'Image'		=> array(
			'fields'	=> array(
				'imagen'	=> array(
					'versions'	=> array(
						array(
							'prefix'	=> 'mini',
							'width'		=> 100,
							'height'	=> 100,
							'crop'		=> true
						),
						array(
							'prefix'	=> 'banner',
							'width'		=> 600,
							'height'	=> 300,
							'crop'		=> true
						)
					)
				)
			)
		)
	);

	/**
	 * VALIDACIONES
	 */
	public $validate = array(
		'nombre' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				'message'		=> 'Debe ingresar un nombre',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'tienda_id' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				'message'		=> 'Debe iseleccionar una tienda',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'activo' => array(
			'boolean' => array(
				'rule'			=> array('boolean'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'sitio_url' => array(
			'url' => array(
				'rule'			=> array('url'),
				'last'			=> true,
				'message'		=> 'Formato de url no válido',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		)
	);

	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'Plantilla' => array(
			'className'				=> 'Plantilla',
			'foreignKey'			=> 'plantilla_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Tienda' => array(
			'className'				=> 'Tienda',
			'foreignKey'			=> 'tienda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);
	public $hasAndBelongsToMany = array(
		'Categoria' => array(
			'className'				=> 'Categoria',
			'joinTable'				=> 'categorias_emails',
			'foreignKey'			=> 'email_id',
			'associationForeignKey'	=> 'categoria_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);

	/**
	 * Callbacks
	 */
	public function beforeSave($created = true, $options = array()) {

		parent::beforeSave($created, $options);

		if ( isset($this->data['Email']) && $created) {

			
			
		}
	}


	public function afterSave($created = true, $options = array()) {

		parent::afterSave($created, $options);

		$semaforo = true;

		if (isset($this->data['Email']) && $this->data['Email']['semaforo'] ) {
			$semaforo = false;
			unset($this->data['Email']['semaforo']);
		}

		if ( isset($this->data['Email']) && $semaforo) {


			
			$htmlPlantilla = ClassRegistry::init('Plantilla')->find('first', array(
					'conditions' => array(
						'Plantilla.id' => $this->data['Email']['plantilla_id']
					),
					'fields' => array('html')
				)
			);

			if ( ! empty($htmlPlantilla['Plantilla']['html']) ) {
		
				// Existe HTML donde reemplazar. Continuamos
				$htmlPlantilla['Plantilla']['html'] = str_replace('[**titulo**]', $this->data['Email']['titulo'], $htmlPlantilla['Plantilla']['html']);
				$htmlPlantilla['Plantilla']['html'] = str_replace('[**fecha**]', $this->data['Email']['fecha'], $htmlPlantilla['Plantilla']['html']);



				// Tiene una imagen
				if ( ! empty($this->data['Email']['imagen']) ) {
					$urlBanner = sprintf('%s/img/%s/%d/%s', Router::url('/', true), $this->alias, $this->id, $this->data['Email']['imagen']);
					$htmlPlantilla['Plantilla']['html'] = str_replace('[**url_banner**]', $urlBanner, $htmlPlantilla['Plantilla']['html']);
				}else{

					$this->Behaviors->disable('Image');

					$htmlImage = $this->find('first', array(
						'conditions' => array(
							'id' => $this->id
						), 
						'fields' => array('imagen')
						)
					);
					
					$urlBanner = sprintf('%s/img/%s/%d/%s', Router::url('/', true), $this->alias, $this->id, $htmlImage['Email']['imagen']);
					$htmlPlantilla['Plantilla']['html'] = str_replace('[**url_banner**]', $urlBanner, $htmlPlantilla['Plantilla']['html']);
				}


				

				// Asignamos el html de la plantilla al newsletter
				$this->data['Email']['html'] = $htmlPlantilla['Plantilla']['html'];

				//Quitamos la imágen del newsletter para evitar problemas
				unset($this->data['Email']['imagen']);
				
				// Guardamos el nuevo html
				$this->save($this->data, array('callbacks' => false));

			}	
		}
	}

	/**
	 * Functión que permite armar un arreglo ordenado con las secciones del html
	 * @param  array() 	$html 	arreglo que contien la data del html
	 * @return array() 	$htmlEmail 	Arreglo con la data ordenada	
	 */
	public function armarHtmlEmail($html = null) {
		// Inicio y fin de la seccion 
		$seccion_inicio 	= strpos($html['Email']['html'], '[**seccion**]');
		$seccion_final 		= strpos($html['Email']['html'], '[*/seccion**]');

		/** 
		  * Pocisiones de las etiquetas para poder tomar sus contenidos
		  */ 
		$bloque2Columnas_i 	= strpos($html['Email']['html'], '[**bloque_dos_columnas**]');
		$bloque2Columnas_f 	= ( strpos($html['Email']['html'], '[*/bloque_dos_columnas**]') - $bloque2Columnas_i);
		$limite2Columnas	= strpos($html['Email']['html'], '[*/bloque_dos_columnas**]');


		$bloque3Columnas_i 	= strpos($html['Email']['html'], '[**bloque_tres_columnas**]');
		$bloque3Columnas_f 	= ( strpos($html['Email']['html'], '[*/bloque_tres_columnas**]') - $bloque3Columnas_i);
		$limite3Columnas	= strpos($html['Email']['html'], '[*/bloque_tres_columnas**]');


		// Contenido de cabecera y footer
		$htmlCabecera 	= substr($html['Email']['html'], 0, $seccion_inicio );
		$htmlFooter 	= substr($html['Email']['html'], $seccion_final );

		// html Bloques de 2 y 3 columnas
		$htmlBloque2Columnas= substr($html['Email']['html'], $bloque2Columnas_i, $bloque2Columnas_f );
		$htmlBloque3Columnas= substr($html['Email']['html'], $bloque3Columnas_i, $bloque3Columnas_f );

		//Contenido de seccion a categoria
		$categoria_inicio 	= strpos($html['Email']['html'], '[**categoria**]');
		$categoria_fin 		= ( strpos($html['Email']['html'], '[*/categoria**]') - $categoria_inicio );

		// Contenido de categoria
		$htmlCategoria		= substr($html['Email']['html'], $categoria_inicio, $categoria_fin);

		$diff_categoria_top		= $categoria_inicio - $seccion_inicio;

		$htmlSeccionTop 		= substr($html['Email']['html'], $seccion_inicio, $diff_categoria_top); 

		$htmlSeccionBottom = '';
		
		// Contenido desde ultimo bloque hasta el fin de la sección
		if ( preg_match('/bloque_dos_columnas/i', $html['Email']['html']) ) {
			$diff_cuerpo_bottom	= $seccion_final - $limite2Columnas;
			$htmlSeccionBottom		= substr($html['Email']['html'], $limite2Columnas, $diff_cuerpo_bottom);
		}

		if ( preg_match('/bloque_tres_columnas/i', $html['Email']['html']) ) {
			$diff_cuerpo_bottom	= $seccion_final - $limite3Columnas;
			$htmlSeccionBottom		= substr($html['Email']['html'], $limite3Columnas, $diff_cuerpo_bottom);
		}

		// Limpiamos las etiquetas del html
		$htmlSeccionTop			= str_replace('[**seccion**]', '', trim($htmlSeccionTop));
		$htmlCategoria			= str_replace('[**categoria**]', '', trim($htmlCategoria));
		$htmlFooter				= str_replace('[*/seccion**]', '', trim($htmlFooter));
		$htmlBloque2Columnas	= str_replace('[*/seccion**]', '', trim($htmlBloque2Columnas));
		$htmlBloque2Columnas	= str_replace('[**bloque_dos_columnas**]', '', trim($htmlBloque2Columnas));
		$htmlBloque3Columnas	= str_replace('[**bloque_tres_columnas**]', '', trim($htmlBloque3Columnas));
		$htmlSeccionBottom		= str_replace('[*/bloque_tres_columnas**]', '', trim($htmlSeccionBottom));

		
		// Cuerpo del email 
		$htmlEmail = array(
			'cabecera' => trim($htmlCabecera),
			'seccion_categoria' => trim($htmlSeccionTop),
			'categoria'			=> trim($htmlCategoria),
			'seccion_bloque'	=> trim($htmlSeccionBottom),
			'bloque_2'			=> trim($htmlBloque2Columnas),
			'bloque_3'			=> trim($htmlBloque3Columnas),
			'footer'			=> trim($htmlFooter)
		);
		
		return $htmlEmail;
	}
}
