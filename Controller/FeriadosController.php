<?php
App::uses('AppController', 'Controller');

class FeriadosController extends AppController
{

  public function api_feriados()
  {

    // if (!$this->request->is('get')) {
		// 	throw new MethodNotAllowedException('Método no permitido');
		// }

		// $token = '';

		// if (isset($this->request->query['token'])) {
		// 	$token = $this->request->query['token'];
		// }

		// # Existe token
		// if (!isset($token)) {
		// 	throw new ForbiddenException('Se requiere token');
		// }

		// # Validamos token
		// if (!ClassRegistry::init('Token')->validar_token($token)) {

		// 	throw new UnauthorizedException("Token a expirado");
		// }

    $this->set(array(
      'feriados' => ClassRegistry::init('Feriado')->find('list', [
        'fields' => [
          'feriado',
          'descripcion',
        ]
      ]),

      '_serialize' => array('feriados')
    ));
  }
}
