<?php
App::uses('AppController', 'Controller');
class PagosimultaneoController extends WebpayAppController
{
	public function transaccion()
	{
		$this->layout = 'ajax';
		$token		= $this->request->query['token'];
		$url		= $this->request->query['url'];

		$this->set(compact('token', 'url'));
	}

}