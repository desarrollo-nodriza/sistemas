<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'Chilexpress.Spreadsheet_Excel_Reader', array('file' => 'tracking/Spreadsheet_Excel_Reader.php'));

class TrackingComponent extends Component
{	
	private $Spreadsheet_Excel_Reader;


	public function initialize(Controller $controller)
	{
		$this->Controller = $controller;
		try
		{
			Configure::load('Chilexpress.chilexpress');
		}
		catch ( Exception $e )
		{
			throw new Exception('No se encontró el archivo Plugin/Config/chilexpress.php');
		}
	}

	/**
	 * Rotorna la infromación de un seguimiento
	 * @param  string 		$file            Ruta del archivo CSV
	 * @param  string 		$tracking_number Número de seguimiento
	 * @return array 		Array con las coincidencias
	 */
	public function leer_excel_tracking ($file = '', $tracking_number = '') 
	{	
		$row = array();
		$cont = 0;
		if (($handle = fopen($file, "r")) !== FALSE && !empty($tracking_number)) {
		  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		    $num = count($data);
		    $data = array_map("utf8_encode", $data);

		    if (in_array($tracking_number, $data)) {
				for ($c=0; $c < $num; $c++) {
			        $row[$cont][] =  $data[$c];
			    }	    	
		    }
	
		    $cont++;
		  }
		  fclose($handle);
		}
		return $row;
	}
}

?>