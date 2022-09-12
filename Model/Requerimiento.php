<?php

class Requerimiento extends AppModel
{

    /**
     * Set Cake config DB
     */
    public $useTable    = 'requerimientos';
    public $primaryKey  = 'id';
    
    public function save($data = null, $validate = true, $fieldList = array())
    {
        // Clear modified field value before each save
        $this->set($data);
        $this->data[$this->alias]['created'] = date('Y-m-d H:i:s');

        return parent::save($this->data, $validate, $fieldList);
    }
}
