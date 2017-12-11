<?php
App::uses('AppController', 'Controller');
class ChilexpressController extends ChilexpressAppController
{	
	public function index(){
		prx('index');
	}

	public function inscripcion()
	{
		$token		= $this->request->query['token'];
		$url		= $this->request->query['url'];

		$this->set(compact('token', 'url'));
	}
}
