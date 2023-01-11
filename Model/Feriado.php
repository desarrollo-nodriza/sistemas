<?php
App::uses('AppModel', 'Model');

class Feriado extends AppModel
{

    public $useTable = 'feriados';

    /**
     * actualizar_feriados_sabado_domingo
     * Añade los fin de semana del año en la base de datos
     * @param  string $inicio string de fecha fecha Y-m-d
     * @param  string $fin string de fecha fecha Y-m-d
     * @param  bool $incluir_sabado
     * @return bool
     */
    public function actualizar_feriados_sabado_domingo($inicio = null, $fin = null, $incluir_sabado = true)
    {
        $inicio                     = $inicio ?? date("Y") . "-01-01";
        $fin                         = $fin ?? (date("Y") + 1) . "-01-01";

        $feriados_sabado_domingo     = $this->calcular_feriados_sabado_domingo($inicio, $fin, $incluir_sabado);
        $feriados                     = $feriados_sabado_domingo['feriados'];
        $holidays                     = $feriados_sabado_domingo['holidays'];

        $feriados_existentes         = Hash::extract(ClassRegistry::init('Feriado')->find('all', [
            'conditions' => [
                'Feriado.feriado' => $holidays
            ]
        ]), "{n}.Feriado.feriado");

        $feriados_persistir = array_filter(
            $feriados,
            function ($v, $k) use ($feriados_existentes) {
                return !in_array($v['feriado'], $feriados_existentes);
            },
            ARRAY_FILTER_USE_BOTH
        );

        if ($feriados_persistir) {
            ClassRegistry::init('Feriado')->create();
            ClassRegistry::init('Feriado')->saveAll($feriados_persistir);
        }
        return true;
    }


    /**
     * calcular_feriados_sabado_domingo
     *
     * @param  string $inicio string de fecha fecha Y-m-d
     * @param  string $fin string de fecha fecha Y-m-d
     * @param  bool $incluir_sabado
     * @return array
     */
    public function calcular_feriados_sabado_domingo($inicio, $fin, $incluir_sabado = true)
    {

        $start          = new DateTime($inicio);
        $end            = new DateTime($fin);
        $period         = new DatePeriod($start, new DateInterval('P1D'), $end);
        $holidays       = [];
        $feriados       = [];

        foreach ($period as $dt) {
            $curr = $dt->format('D');

            if (($curr == 'Sat' && $incluir_sabado) || $curr == 'Sun') {
                $holidays[] = $dt->format('Y-m-d');
                $feriados[] = [
                    'feriado'        => $dt->format('Y-m-d'),
                    'descripcion'    => $curr == 'Sat' ? 'Sábado' : 'Domingo',
                ];
            }
        }

        return [
            'holidays' => $holidays,
            'feriados' => $feriados,
        ];
    }

    /**
     * actualizar_feriados
     * Añade los feriados del año en la base de datos 
     * @return bool 
     */
    public function actualizar_feriados()
    {

        $respuesta          = false;
        $feriados_persistir = [];
        $consultar_feriados = $this->consultar_feriados();

        if ($consultar_feriados['code'] == 200) {

            $fechas     = Hash::extract($consultar_feriados['response'], "{n}.fecha");
            $feriados   = array_map(function ($data) {
                return [
                    'feriado'        => $data['fecha'],
                    'descripcion'    => $data['nombre']
                ];
            }, $consultar_feriados['response']);

            $feriados_existentes         = Hash::extract(ClassRegistry::init('Feriado')->find('all', [
                'conditions' => [
                    'Feriado.feriado' => $fechas
                ]
            ]), "{n}.Feriado.feriado");

            $feriados_persistir = array_filter(
                $feriados,
                function ($v, $k) use ($feriados_existentes) {
                    return !in_array($v['feriado'], $feriados_existentes);
                },
                ARRAY_FILTER_USE_BOTH
            );

            if ($feriados_persistir) {
                ClassRegistry::init('Feriado')->create();
                ClassRegistry::init('Feriado')->saveAll($feriados_persistir);
                $respuesta = true;
            }
        }

        return $respuesta;
    }

    /**
     * consultar_feriados
     *
     * @param  string $ano Año de la consulta de feriados
     * @return array
     */
    public function consultar_feriados($ano = null)
    {

        $ano    = $ano ?? date("Y");
        $curl   = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => "https://apis.digital.gob.cl/fl/feriados/$ano",
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'GET',
        ));

        $response   = curl_exec($curl);
        $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);

        curl_close($curl);

        return [
            "code"          => $http_code,
            "response"      => json_decode($response, true),
            "curl_error"    => $curl_error
        ];
    }
}
