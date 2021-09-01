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
        $this->create();
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
            return array(
                'EstadoEnvio' => array(
                    'id' => $this->id,
                    'estado_envio_categoria_id' => $this->field('estado_envio_categoria_id'),
                    'nombre' => $this->field('nombre'),
                    'origen' => $this->field('origen'),
                    'leyenda' => $this->field('leyenda'),
                )
            );
        }

        return false;

    }


    /**
     * Obtiene un estado por el nombre
     */
    public function obtener_por_nombre($nombre, $origen = null)
    {
        $conditions = [
            'nombre' => $nombre,
            'origen' => $origen
        ];
        $conditions = array_filter($conditions);
        return $this->find('first', array(
            'conditions' => $conditions
        ));
    }
    

    /**
     * obtener_id_por_nombre
     *
     * @param  mixed $nombre
     * @return void
     */
    public function obtener_id_por_nombre($nombre)
    {
        $res = $this->obtener_por_nombre($nombre);
    
        if ($res)
        {
            return $res['EstadoEnvio']['id'];
        }

        return false;
    }


        
    /**
     * obtener_estado_envio
     *
     * @param  mixed $id
     * @return void
     */
    public function obtener_estado_envio($id)
    {
        return $this->find('first', array(
            'conditions' => array(
                'id' => $id
            )
        ));
    }

}