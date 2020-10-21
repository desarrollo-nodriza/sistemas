<?php 

# /var/www/html/sistemav2/ && ~/cakephp/lib/Cake/Console/cake limipiar_dtes >/dev/null 2>&1
class LimpiarDtesShell extends AppShell {

	public function main() {

        $log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Dtes',
			'modulo_accion' => 'Inicia proceso de limpiaza de dtes mal emitidos: ' . date('Y-m-d H:i:s')
        ));
        
        $dtes = ClassRegistry::init('Dte')->obtener_dtes_mal_emitidos();
        
        $log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Dtes',
			'modulo_accion' => 'Dtes encontrados : ' . json_encode($dtes)
        ));

        $limpiar = 0;

        if (!empty($dtes)) 
        {
            $limpiar = ClassRegistry::init('Dte')->limpiar_dte();
        }

        $eliminados = ($limpiar == 1) ? count($dtes) : 0;

        $log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Dtes',
			'modulo_accion' => 'Resultado: Dtes mal emitidos = ' . count($dtes) . ' - Eliminados = ' . $eliminados 
        ));

        ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

        return;

	}

}