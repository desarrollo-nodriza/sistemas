<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'GoogleApi', array('file' => 'google-api-php-client' . DS . 'src' . DS . 'Google' . DS . 'autoload.php'));
class GoogleComponent extends Component
{
	public $cliente;
	public $oauth;
	public $plus;

	public function __construct(ComponentCollection $collection, $settings = array())
	{
		$this->cliente		= new Google_Client();
		$this->cliente->setApplicationName($settings['applicationName']);
		$this->cliente->setDeveloperKey($settings['developerKey']);
		$this->cliente->setClientId($settings['clientId']);
		$this->cliente->setClientSecret($settings['clientSecret']);
		$this->cliente->setApprovalPrompt($settings['approvalPrompt']);
		$this->cliente->setAccessType($settings['accessType']);
		$this->cliente->setScopes($settings['scopes']);
	}

	public function oauth()
	{
		$this->oauth		= new Google_Auth_OAuth2($this->cliente);
		return $this->oauth;
	}

	public function plus()
	{
		$this->plus			= new Google_Service_Plus($this->cliente);
		return $this->plus;
	}
}
