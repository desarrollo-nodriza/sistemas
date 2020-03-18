<?php
App::uses('AppController', 'Controller');

class DteComprasController extends AppController
{	
	/**
	 * Lista y filtra losdtes
	 * Endpoint :  /api/dtes.json
	 */
    public function api_index() {

    	$token = '';

    	if (isset($this->request->query['token'])) {
    		$token = $this->request->query['token'];
    	}

    	# Existe token
		if (!isset($token)) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($token)) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

    	$qry = array();

    	$paginacion = array(
        	'limit' => 0,
        	'offset' => 0,
        	'total' => 0
        );

    	if (isset($this->request->query['id'])) {
    		if (!empty($this->request->query['id'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'DteCompra.id' => $this->request->query['id'])));
    		}
    	}

    	if (isset($this->request->query['limit'])) {
    		if (!empty($this->request->query['limit'])) {
    			$qry = array_replace_recursive($qry, array('limit' => $this->request->query['limit']));
    			$paginacion['limit'] = $this->request->query['limit'];
    		}
    	}

    	if (isset($this->request->query['offset'])) {
    		if (!empty($this->request->query['offset'])) {
    			$qry = array_replace_recursive($qry, array('offset' => $this->request->query['offset']));
    			$paginacion['offset'] = $this->request->query['offset'];
    		}
    	}

    	if (isset($this->request->query['folio'])) {
    		if (!empty($this->request->query['folio'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'DteCompra.folio' => $this->request->query['folio'])));
    		}
    	}

    	if (isset($this->request->query['rut_emisor'])) {
    		if (!empty($this->request->query['rut_emisor'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'DteCompra.rut_emisor' => $this->request->query['rut_emisor'])));
    		}
    	}

    	if (isset($this->request->query['tipo_documento'])) {
    		if (!empty($this->request->query['tipo_documento'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'DteCompra.tipo_documento' => $this->request->query['tipo_documento'])));
    		}
    	}

    	if (isset($this->request->query['fecha_emision'])) {
    		if (!empty($this->request->query['fecha_emision'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'DteCompra.fecha_emision' => $this->request->query['fecha_emision'])));
    		}
    	}
   
        $dtes = $this->DteCompra->find('all', $qry);

    	$paginacion['total'] = count($dtes);

        $this->set(array(
            'dtes' => $dtes,
            'paginacion' => $paginacion,
            '_serialize' => array('dtes', 'paginacion')
        ));
    }
}