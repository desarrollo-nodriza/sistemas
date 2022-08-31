<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'Pushy', array('file' => 'Pushy/Pushy.php'));

class PushyComponent extends Component
{
    private $Pushy;
    private $apiKey;

    public function __construct()
    {
        $this->Pushy  = new Pushy();
        $this->apiKey = ClassRegistry::init('Tienda')->tienda_principal('api_key_pushy')['Tienda']['api_key_pushy'];
    }

    /**
     * sendPushNotification
     * Para enviar a mas de un se deben enviar tokens separados por coma
     * @param  mixed $data
     * @param  mixed $to
     * @return void
     */
    public function sendPushNotification($data, $to)
    {
        $this->Pushy->sendPushNotification($data, $to, $this->apiKey);
    }
}
