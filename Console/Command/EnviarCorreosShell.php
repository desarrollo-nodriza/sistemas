<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('View', 'View');
class EnviarCorreosShell extends AppShell
{
	public $uses			= array('Correo');
	public $CakeEmail		= null;
	public $host			= 'localhost';

	public function main()
	{
		/**
		 * Instancia CakeEmail
		 */
		if ( ! $this->CakeEmail || ! $this->View )
		{
			$this->CakeEmail = new CakeEmail('gmail');
		}

		/**
		 * Obtiene los mails que no han sido enviados
		 */
		$correos				= $this->Correo->find('all', array(
			'conditions'		=> array(
				'Correo.enviado'		=> false
			),
			'callbacks'			=> false
		));

		/**
		 * Estadisticas de envio
		 */
		$total				= count($correos);
		$procesados			= 0;
		$enviados			= 0;
		$erroneos			= 0;

		/**
		 * Recorre todos los mails e intenta el envio
		 */
		foreach ( $correos as $email )
		{
			if ( $this->enviar($email) )
			{
				$enviados++;
			}
			else
			{
				$erroneos++;
			}

			$procesados++;
		}

		/**
		 * Imprime las estadisticas
		 */
		$this->out(sprintf('%s: %02d', str_pad('Emails por enviar', 35), $total));
		$this->out(sprintf('%s: %02d', str_pad('Emails procesados', 35), $procesados));
		$this->out(sprintf('%s: %02d', str_pad('Emails enviados correctamente', 35), $enviados));
		$this->out(sprintf('%s: %02d', str_pad('Emails con error de envio', 35), $erroneos));
		$this->out(sprintf('%s: %s', str_pad('DataSource', 35), $this->Correo->useDbConfig));
		$this->hr();
	}

	public function enviar($datos = array())
	{
		if ( ! $datos )
		{
			return false;
		}

		try
		{
			$this->CakeEmail
				->reset()
				//->config('gmail')
				->emailFormat('html')
				->domain($this->host)
				->from(array($datos['Correo']['remitente_email'] => $datos['Correo']['remitente_nombre']))
				->subject(sprintf('%s', $datos['Correo']['asunto']));

			/**
			 * Destinatarios
			 */
			$destinatarios			= array_map(function($email) { return trim($email); }, explode(',', $datos['Correo']['destinatario_email']));
			foreach ( $destinatarios as $destinatario )
			{
				$this->CakeEmail->addTo($destinatario, $datos['Correo']['destinatario_nombre']);
			}

			if ( $datos['Correo']['cc_email'] )
			{
				$copias					= array_map(function($email) { return trim($email); }, explode(',', $datos['Correo']['cc_email']));
				foreach ( $copias as $copia )
				{
					$this->CakeEmail->addCc($copia);
				}
			}
			if ( $datos['Correo']['bcc_email'] )
			{
				$copiasOcultas			= array_map(function($email) { return trim($email); }, explode(',', $datos['Correo']['bcc_email']));
				foreach ( $copiasOcultas as $copiaOculta )
				{
					$this->CakeEmail->addBcc($copiaOculta);
				}
			}

			$formato		= true;
		}
		catch ( SocketException $e )
		{
			$formato		= false;
		}

		if ( ! $formato )
		{
			return false;
		}

		try
		{
			$this->CakeEmail->send($datos['Correo']['html']);
			$enviado		= true;
		}
		catch ( SocketException $e )
		{
			$enviado		= false;
		}
		finally
		{	
			$this->Correo->id		= $datos['Correo']['id'];
			$this->Correo->save(array(
				'procesado'			=> true,
				'enviado'			=> $enviado,
				'reintentos'		=> ($datos['Correo']['reintentos'] + 1)
			));

		}

		return $enviado;
	}
}
