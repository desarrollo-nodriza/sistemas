<?php
App::uses('AppModel', 'Model');
class EstadoEnvioCategoria extends AppModel
{	

    public $belongsTo = array(
		'VentaEstado' => array(
			'className'				=> 'VentaEstado',
			'foreignKey'			=> 'venta_estado_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
        )
    );

	public $hasMany = array(
		'EstadoEnvio' => array(
			'className'				=> 'EstadoEnvio',
			'foreignKey'			=> 'estado_envio_categoria_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		)
	);

    public function obtener_default_id()
    {
        $default = $this->find('first', array(
            'conditions' => array(
                'default' => 1
            ),
            'order' => array('id', 'desc')
        ));

        if (empty($default))
        {
            return false;
        }

        return $default['EstadoEnvioCategoria']['id'];
    }
}
