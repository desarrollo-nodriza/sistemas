<?php
/**
 * Generar QRs en la vista
 */

App::uses('AppHelper', 'Helper');
App::import('Vendor', 'QrCode', array('file' => 'QRCode/vendor/autoload.php'));

use Da\QrCode\QrCode;

class QrCodeHelper extends AppHelper 
{   

    /*
	 * Constructor
	 */
	public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
    }


    public function generarQr($texto, $size = 250, $margin = 5)
    {
        $qrCode = (new QrCode($texto))
        ->setSize($size)
        ->setMargin($margin);

        return $qrCode->writeDataUri();
    }
}