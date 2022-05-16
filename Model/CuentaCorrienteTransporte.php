<?php

class CuentaCorrienteTransporte extends AppModel
{

    public $displayField    = 'nombre';
    public $useDbConfig     = 'default';
    public $useTable        = 'cuenta_corriente_transporte';

    public $hasMany = array(
        'ValorAtributoCuentaCorrienteTransporte' => array(
            'className'    => 'ValorAtributoCuentaCorrienteTransporte',
            'foreignKey'   => 'cuenta_corriente_transporte_id',
            'conditions'   => '',
            'fields'       => '',
            'order'        => '',
            'counterCache' => true,
        )
    );

    public function selector()
    {
        $datos = $this->find('all', [
            'fields' => ['id', "nombre", 'dependencia'],
            'conditions' => ['activo' => true]
        ]);

        $CuentaCorrienteTransporte = [];

        foreach ($datos as $value) {
            $CuentaCorrienteTransporte[$value['CuentaCorrienteTransporte']['id']] = "{$value['CuentaCorrienteTransporte']['nombre']} | Dependencia {$value['CuentaCorrienteTransporte']['dependencia']} ";
        }

        return $CuentaCorrienteTransporte;
    }

    public function valor_atributos($id)
    {
        $datos = $this->find('first', [
            'fields' => ['id', "nombre", 'dependencia'],
            'contain' => [
                'ValorAtributoCuentaCorrienteTransporte' => ['TablaAtributo']
            ],
            'conditions' => ['id' => $id]
        ]);

        $return = [];

        if ($datos['ValorAtributoCuentaCorrienteTransporte'] ?? false) {
            foreach ($datos['ValorAtributoCuentaCorrienteTransporte'] as $value) {
                $return[$value['TablaAtributo']['nombre_referencia']] = $value['valor'];
            }
        }

        return $return;
    }

    public function dependencia($id)
	{	
		$this->id = $id;
		return $this->field('dependencia');
	}
}
