<?php
	
	public function leer_excel_cristian () {

		if ($this->request->is('post')) {

			require_once 'backend/assets/excel-reader.php';

			//tomas el archivo del input file y listo!

			$DataExcel = new Spreadsheet_Excel_Reader($this->request->data['input_name']['tmp_name']);

			prx($DataExcel);

		}

	}

?>