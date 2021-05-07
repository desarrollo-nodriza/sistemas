<?php
App::uses('AppModel', 'Model');
class EstadoEnvio extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';

    public $hasMany = array(
		'EnvioHistorico' => array(
			'className'				=> 'EnvioHistorico',
			'foreignKey'			=> 'estado_envio_id',
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

    /**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'EstadoEnvioCategoria' => array(
			'className'				=> 'EstadoEnvioCategoria',
			'foreignKey'			=> 'estado_envio_categoria_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		)
	);

    // Callback
    public function beforeSave($options = array())
	{
        

        return true;
    }


    /**
     * Obtiene el id de un estado por el nombre, además
     * permite crearlo si no existe.
     * 
     * @param text $nombre  Nombre del estado
     * @param text $tipo    Tipo de estado (creado, en_reparto, enviado, entregado, error)
     * @param int $venta_estado_id  Identificador del estado de venta relacionado (usado para actualizar las ventas segun el estado proveedor)
     * 
     * @return mixed
     */
    public function crear($nombre, $categoria = '', $origen = 'No definido', $leyenda = '')
    {
        # No existe el estado, lo creamos
        $estado = array(
            'EstadoEnvio' => array(
                'nombre' => $nombre,
                'estado_envio_categoria_id' => (empty($categoria)) ? $categoria : ClassRegistry::init('EstadoEnvioCategoria')->obtener_default_id(),
                'origen' => $origen,
                'leyenda' => $leyenda
            )
        );

        if ($this->save($estado))
        {
            return $this->id;
        }

        return false;

    }


    /**
     * Obtiene un estado por el nombre
     */
    public function obtener_por_nombre($nombre)
    {
        return $this->find('first', array(
            'conditions' => array(
                'nombre' => $nombre
            )
        ));
    }

    public function obtener_id_por_nombre($nombre)
    {
        $res = $this->obtener_por_nombre($nombre);
    
        if ($res)
        {
            return $res['EstadoEnvio']['id'];
        }

        return false;
    }

}