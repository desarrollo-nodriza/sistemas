<?php 

App::uses('Component', 'Controller');

class VolumetricaComponent extends Component
{   
    public function getContainerDimensions($cajas)
    {   
        $volumen_total = 0;

        foreach ($cajas as $c => $caja)
        {   
            # Validamos los indices requeridos
            if (!array_key_exists('height', $caja) ||
                 !array_key_exists('length', $caja) ||
                 !array_key_exists('width', $caja) ||
                 !array_key_exists('quantity', $caja))
            {
                throw new InvalidArgumentException("Esta funcionalidad requiere los siguientes indices en el arreglo: height, width, length, quantity. \$caja");
            }

            $volumen_total = $volumen_total + $this->_calcVolumeBox($caja['height'], $caja['width'], $caja['length'], $caja['quantity']);        
        
        }

        if (!$volumen_total)
        {
            throw new Exception("Las dimensiones indicadas en las cajas no permite calculo del volumen del contenedor. \$volumen_total");
        }


        $raiz_cubica = round(pow($volumen_total, 1/3), 0);

        return [
            'width' => $raiz_cubica,
            'height' => $raiz_cubica,
            'length' => $raiz_cubica,
            'volume' => round($volumen_total, 0)
        ];

    }

    private function _calcVolumeBox($alto, $ancho, $largo, $cantidad)
    {
        return ($alto*$ancho*$largo) * (int) $cantidad;
    }

}