<?php
class ImageBehavior extends ModelBehavior
{
	/**
	 * Configuracion de usuario + defualt
	 * @var				array
	 * @access			public
	 */
	public $settings		= array();

	/**
	 * Tipos permitidos de imagenes
	 * @var				array
	 * @access			public
	 */
	public $image_types		= array('jpg', 'jpeg', 'png', 'gif');


	/**
	 * Configura el Behavior cuando se atacha a un modelo
	 *
	 * @param			Model			$model				El modelo al cual se atacha
	 * @param			array			$config				La configuracion del Behavior
	 * @return			void
	 * @access			public
	 */
	public function setup(Model $model, $config = array())
	{
		$settings			= Set::merge(array('baseDir' => '', 'fields' => array()), $config);
		$fields				= array();

		foreach ( $settings['fields'] as $key => $value )
		{
			$field			= (is_numeric($key) ? $value : $key);
			$conf			= (is_numeric($key) ? array() : (is_array($value) ? $value : array()));
			$conf			= Set::merge(array(
				'name'				=> '',
				'image_types'		=> array(),
				/*
				'thumbnail'			=> array(
					'prefix'			=> 'thumb',
					'create'			=> false,
					'width'				=> 100,
					'height'			=> 100,
					'aspect'			=> true,
					'allow_enlarge'		=> false,
				),
				*/
				'resize'			=> null,
				'versions'			=> array(),
			), $conf);

			if ( is_array($conf['resize']) )
			{
				$conf['resize']		= Set::merge(array(
					'aspect'			=> true,
					'allow_enlarge'		=> false,
				), $conf['resize']);
			}

			foreach ( $conf['versions'] as &$version )
			{
				$version		= Set::merge(array(
					'aspect'			=> true,
					'allow_enlarge'		=> false,
				), $version);
			}

			if ( empty($conf['image_types']) )
				$conf['image_types'] 	= $this->image_types;

			$settings['fields'][$field]		= $conf;
		}

		$this->settings[$model->name] = $settings;
	}


	/**
	 * Validaciones antes de guardar un registro a la DB
	 *
	 * @param			Model			$model				Modelo de donde se esta guardando el registro
	 * @return			boolean								Resultado de la operacion
	 * @access			public
	 * @todo												Parametrizar renombramiento de archivo via config de usuario
	 */
	public function beforeSave(Model $model, $options = array())
	{
		$tempData	= array();
		extract($this->settings[$model->name]);

		foreach ( $fields as $key => $value )
		{
			$field		= (is_numeric($key) ? $value : $key);
			if ( isset($model->data[$model->name][$field]) )
			{
				$ext		= explode('.', $model->data[$model->name][$field]['name']);
				$ext		= end($ext);
				// @TODO - Parametrizar renombramiento de archivo via config de usuario
				$model->data[$model->name][$field]['name'] = preg_replace('/[^a-z0-9_\.]/', '', str_replace(array(' ', '-'), array('_', '_'), strtolower($model->data[$model->name][$field]['name'])));

				if ( $this->__isUploadFile($model->data[$model->name][$field] ) &&
					 $this->__isValidExtension($this->settings[$model->name]['fields'][$field]['image_types'], $model->data[$model->name][$field]) )
				{
					$tempData[$field]					= $model->data[$model->name][$field];
					$model->data[$model->name][$field]	= $model->data[$model->name][$field]['name'];
				}
				else
					unset($model->data[$model->name][$field]);
			}
		}

		$this->runtime[$model->name]['beforeSave']	= $tempData;
		return true;
	}


	/**
	 * Guarda los archivos una vez guardado el registro en la DB
	 *
	 * @param			Model			$model				Modelo de donde se guardo el registro
	 * @param			object			$created			Description
	 * @return			object								Description
	 * @access			public
	 */
	public function afterSave(Model $model, $created, $options = array())
	{
		$tempData	= $this->runtime[$model->name]['beforeSave'];
		unset($this->runtime[$model->name]['beforeSave']);

		foreach ( $tempData as $field => $value )
			$this->__saveFile($model, $field, $value);

		return true;
	}


	/**
	 * Agrega las imagenes y sus recortes a los registros devueltos por find*
	 *
	 * @param			Model			$model				Modelo donde se estan buscando los registros
	 * @param			array			$results			Resultados devueltos por find*
	 * @param			boolean			$primary			Si la busqueda se realizo en el modelo principal
	 * @return			array								Resultados modificados
	 * @access			public
	 */
	public function afterFind(Model $model, $results, $primary = true)
	{
		extract($this->settings[$model->name]);

		if ( is_array($results) && ! empty($results) )
		{
			/**
			 * Find All
			 */
			$i = 0;
			if ( isset($results[0]) )
			{
				while ( isset($results[$i][$model->name]) && is_array($results[$i][$model->name]) )
				{
					foreach ( $fields as $field => $fieldParams )
					{
						if ( isset($results[$i][$model->name][$field]) && ($results[$i][$model->name][$field] != '') )
						{
							$value								= $results[$i][$model->name][$field];
							$results[$i][$model->name][$field]	= $this->__getParams($model, $field, $value, $fieldParams, $results[$i][$model->name]);
						}
					}
					$i++;
				}
			}

			/**
			 * Find First
			 */
			else
			{
				foreach ( $fields as $field => $fieldParams )
				{
					if ( isset($results[$model->name][$field]) && ($results[$i][$model->name][$field] != '' ) )
					{
						$value								= $results[$i][$model->name][$field];
						$results[$model->name][$field]		= $this->__getParams($model, $field, $value, $fieldParams, $results[$model->name]);
					}
				}
			}
		}

		return $results;
	}


	/**
	 * Antes de realizar la eliminacion del registro, se ignoran conexiones abortadas por cliente
	 *
	 * @param			Model			$model				Modelo desde donde se elimina el registro
	 * @param			boolean			$cascade			Eliminacion en cascada
	 * @return			boolean								Resultado de la operacion
	 * @access			public
	 */
	public function beforeDelete(Model $model, $cascade = true)
	{
		$this->runtime[$model->name]['ignoreUserAbort']	= ignore_user_abort();
		@ignore_user_abort(true);
		return true;
	}


	/**
	 * Elimina la imagen luego de eliminar un registro
	 *
	 * @param			Model			$model				Modelo donde se elimino el registro
	 * @return			boolean								Resultado de la operacion
	 * @access			public
	 */
	public function afterDelete(Model $model)
	{
		extract($this->settings[$model->name]);
		App::uses('Folder', 'Utility');

		foreach  ( $fields as $field => $fieldParams )
		{
			$folderPath		= $this->__getFullFolder($model);
			$folder			= new Folder($folderPath, false);
			if ( $folder->pwd() )
				@$folder->delete($folder->pwd());
		}

		@ignore_user_abort((bool) $this->runtime[$model->name]['ignoreUserAbort']);
		unset($this->runtime[$model->name]['ignoreUserAbort']);
		return true;
	}


	/**
	 * Comprueba que el archivo exista y sea valido
	 *
	 * @param			string			$file				Datos del archivo
	 * @return			boolean								Resultado de la validacion
	 * @access			private
	 */
	private function __isUploadFile($file)
	{
		if ( ! isset($file['tmp_name']) || ( isset($file['error']) && $file['error'] > 0 ) || ! is_uploaded_file($file['tmp_name']) )
			return false;

		return file_exists($file['tmp_name']);
	}


	/**
	 * Comprueba que la extension del archivo que se subio sea permitida por configuracion
	 *
	 * @param			array			$image_types		Arreglo de extensiones permitidas
	 * @param			array			$file				Datos del archivo
	 * @return			bollean								Resultado de la validacion
	 * @access			private
	 */
	private function __isValidExtension($image_types, $file)
	{
		return in_array($this->__decodeContent($file['type']), $image_types);
	}


	/**
	 * Obtiene las distintas versiones de una imagen para ser inyectados en resultados de find
	 *
	 * @param			Model			$model				Modelo donde se realiza el find
	 * @param			string			$field				Campo de imagen
	 * @param			string			$value				Nombre de la imagen
	 * @param			array			$fieldParams		Configuracion del behavior para esta imagen
	 * @param			array			$record				Resultado unico del find
	 * @return			array								Resultados inyectados con las versiones de las imagenes
	 * @access			private
	 */
	private function __getParams(Model $model, $field, $value, $fieldParams, $record)
	{
		extract($this->settings[$model->name]);
		$result		= array();

		if ( $value != '' )
		{
			$folderName			= $this->__getFolder($model, $record);
			$fileName			= $this->__decodeContent($value);
			$ext				= explode('.', strtolower($fileName));
			$ext				= end($ext);
			$result['path']		= $folderName . $fileName;

			if ( in_array($ext, $this->image_types) )
			{
				foreach ( $fields[$field]['versions'] as $version )
					$result[$this->__getPrefix($version)]	= $folderName . $this->__getPrefix($version) . '_' . $fileName;
			}
		}

		return $result;
	}


	/**
	 * Obtiene la ruta completa donde se debe guardar el archivo
	 *
	 * @param			Model			$model				Modelo de donde se obtendra la ruta
	 * @return			string								Ruta completa donde se aloja(ra) el archivo
	 * @access			private
	 */
	private function __getFullFolder(Model $model)
	{
		extract($this->settings[$model->name]);
		return WWW_ROOT . basename(IMAGES_URL) . implode(DS, array($baseDir, Inflector::camelize($model->name), $model->id)) . DS;
	}


	/**
	 * Obtiene la carpeta donde esta el archivo
	 *
	 * @param			Model			$model				Modelo del cual se obtendra la ruta
	 * @param			array			$record				Registro del cual se obtendra la ruta
	 * @return			string								Ruta donde esta alojado el archivo
	 * @access			private
	 */
	private function __getFolder(Model $model, $record)
	{
		return Inflector::camelize($model->name) . '/' . $record[$model->primaryKey] . '/';
	}


	/**
	 * Obtiene el prefijo de una version de corte
	 *
	 * @param			array			$fieldParams		Configuracion de la version
	 * @return			string								Prefijo de la version dada
	 * @access			private
	 */
	private function __getPrefix($fieldParams)
	{
		return (isset($fieldParams['prefix']) ? $fieldParams['prefix'] : "{$fieldParams['width']}x{$fieldParams['height']}");
	}


	/**
	 * Obtiene la extension del archivo basado en su MIME Type
	 *
	 * @param			string			$content			MIME Type
	 * @return			string								Extension correspondiente al MIME Type
	 * @access			private
	 */
	private function __decodeContent($content)
	{
		$contentsMaping	= array(
			'image/gif'							=> 'gif',
			'image/jpeg'						=> 'jpg',
			'image/pjpeg'						=> 'jpg',
			'image/x-png'						=> 'png',
			'image/jpg'							=> 'jpg',
			'image/png'							=> 'png',
			'application/x-shockwave-flash'		=> 'swf',
			'application/pdf'					=> 'pdf',
			'application/pgp-signature'			=> 'sig',
			'application/futuresplash'			=> 'spl',
			'application/msword'				=> 'doc',
			'application/postscript'			=> 'ps',
			'application/x-bittorrent'			=> 'torrent',
			'application/x-dvi'					=> 'dvi',
			'application/x-gzip'				=> 'gz',
			'application/x-ns-proxy-autoconfig'	=> 'pac',
			'application/x-shockwave-flash'		=> 'swf',
			'application/x-tgz'					=> 'tar.gz',
			'application/x-tar'					=> 'tar',
			'application/zip'					=> 'zip',
			'audio/mpeg'						=> 'mp3',
			'audio/x-mpegurl'					=> 'm3u',
			'audio/x-ms-wma'					=> 'wma',
			'audio/x-ms-wax'					=> 'wax',
			'audio/x-wav'						=> 'wav',
			'image/x-xbitmap'					=> 'xbm',
			'image/x-xpixmap'					=> 'xpm',
			'image/x-xwindowdump'				=> 'xwd',
			'text/css'							=> 'css',
			'text/html'							=> 'html',
			'text/javascript'					=> 'js',
			'text/plain'						=> 'txt',
			'text/xml'							=> 'xml',
			'video/mpeg'						=> 'mpeg',
			'video/quicktime'					=> 'mov',
			'video/x-msvideo'					=> 'avi',
			'video/x-ms-asf'					=> 'asf',
			'video/x-ms-wmv'					=> 'wmv'
		);

		return (isset($contentsMaping[$content]) ? $contentsMaping[$content] : $content);
	}


	/**
	 * Guarda el archivo en disco
	 *
	 * @param			Model			$model				Modelo donde se guardo el registro
	 * @param			string			$field				Nombre del campo de archivo
	 * @param			array			$fileData			Datos del archivo
	 * @return			boolean								Resultado de la operacion
	 * @access			private
	 * @todo												Parametrizar sobreescritura de archivo via config de usuario
	 */
	private function __saveFile(Model $model, $field, $fileData)
	{
		extract($this->settings[$model->name]);
		$folderName		= $this->__getFullFolder($model);
		$fileName		= $this->__decodeContent($fileData['name']);
		$ext			= explode('.', $fileName);
		$ext			= end($ext);

		App::uses('Folder', 'Utility');
		App::uses('File', 'Utility');

		$folder			= new Folder($path = $folderName, true, $mode = 0777);
		$fullFile		= $folder->pwd() . $fileName;
		$file			= new File($fullFile);

		/**
		 * @TODO - Parametrizar sobreescritura de archivo
		 */
		if ( $file->exists() )
			$file->delete();

		copy($fileData['tmp_name'], $fullFile);

		if ( in_array($ext, $this->image_types) )
		{
			foreach ( $fields[$field]['versions'] as $version )
			{
				$newFile		= $this->__getPrefix($version) . '_' . basename($fileName);
				if ( ! $this->__resize($folder->pwd(), $fileName, $newFile, $field, $version) )
					return false;
			}
		}

		return true;
	}


	/**
	 * Realiza los cortes de la imagen
	 *
	 * @param			string			$folder				Ruta donde esta la imagen original
	 * @param			string			$originalName		Nombre de la imagen original
	 * @param			string			$newName			Nombre de la imagen cortada que se creara
	 * @param			string			$field				Campo de imagen correpondiente
	 * @param			array			$fieldParams		Configuracion del behavior para esta imagen
	 * @return			boolean								Resultado de la operacion
	 * @access			private
	 */
	private function __resize($folder, $originalName, $newName, $field, $fieldParams)
	{
		$types				= array(1 => 'gif', 'jpeg', 'png', 'swf', 'psd', 'wbmp');
		$fullpath			= $folder;
		$url				= $folder . DS . $originalName;

		if ( ! ( $size = getimagesize($url) ) )
			return false;

		$width				= $fieldParams['width'];
		$height				= $fieldParams['height'];

		$original_width		= $fieldParams['width'];
		$original_height	= $fieldParams['height'];

		if ( isset($fieldParams['crop']) && $fieldParams['crop'] === true )
		{
			if ( ($size[1] / $height) < ($size[0] / $width) )
				$width		= ceil(($size[0] / $size[1]) * $height);
			else
				$height		= ceil($width / ($size[0] / $size[1]));
		}
		else
		{
			if ( ($size[1] / $height) > ($size[0] / $width) )
				$width		= ceil(($size[0] / $size[1]) * $height);
			else
				$height		= ceil($width / ($size[0] / $size[1]));
		}


		if ( $fieldParams['allow_enlarge'] === false )
		{
			if ( ($width > $size[0]) || ($height > $size[1]) )
			{
				$width		= $size[0];
				$height		= $size[1];
			}
		}
		else
		{
			if ( $fieldParams['aspect'] )
			{
				if ( ($size[1] / $height) > ($size[0] / $width) )
					$width		= ceil(($size[0] / $size[1]) * $height);
				else
					$height		= ceil($width / ($size[0] / $size[1]));
			}
		}

		$cachefile		= $fullpath . DS . $newName;

		if ( file_exists($cachefile) )
		{
			$csize		= getimagesize($cachefile);
			$cached		= ($csize[0] == $width && $csize[1] == $height);
			if ( @filemtime($cachefile) < @filemtime($url) )
				$cached		= false;
		}
		else
			$cached		= false;

		if ( ! $cached )
			$resize		= ($size[0] > $width || $size[1] > $height) || ($size[0] < $width || $size[1] < $height || ($fieldParams['allow_enlarge'] === false));
		else
			$resize		= false;

		if ( $resize )
		{
			$image		= call_user_func('imagecreatefrom' . $types[$size[2]], $url);
			if ( function_exists('imagecreatetruecolor') && ($temp = imagecreatetruecolor($width, $height)) )
			{
				@call_user_func('image' . $types[$size[2]], $temp, 100);
				imagealphablending($temp, false);
				imagesavealpha($temp, true);
				imagecopyresampled($temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
			}
			else
			{
				$temp		= imagecreate($width, $height);
				imagecopyresized($temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
			}

			if ( isset($fieldParams['crop']) && $fieldParams['crop'] === true )
			{
				if ( function_exists('imagecreatetruecolor') && ($croped = imagecreatetruecolor($original_width, $original_height)) )
				{
					@call_user_func('image' . $types[$size[2]], $croped, 100);
					imagealphablending($croped, false);
					imagesavealpha($croped, true);
					imagecopy($croped, $temp, (floor($original_width / 2) - floor($width / 2)), (floor($original_height / 2) - floor($height / 2)), 0, 0, $width, $height);
				}
				else
				{
					$croped		= imagecreate ($original_width, $original_height);
					imagecopy($croped, $temp, (floor($original_width / 2) - floor($width / 2)), (floor($original_height / 2) - floor($height / 2)), 0, 0, $width, $height);
				}
				call_user_func('image' . $types[$size[2]], $croped, $cachefile);
				imagedestroy($croped);
			}
			else
			{
				call_user_func('image' . $types[$size[2]], $temp, $cachefile);
			}

			imagedestroy($image);
			imagedestroy($temp);
		}

		return true;
	}
}
