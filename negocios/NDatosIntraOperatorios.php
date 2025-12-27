<?php
require_once("../datos/DDatosIntraOperatorios.php");

class NDatosInstraOperatorios{
    private $capaDatos;

    public function __construct(){
        $this->capaDatos = new DDatosIntraOperatorios();
    }

    public function saveDatosIntraOperatorios($data){
        try{
            $data['created_at'] = date("Y-m-d H:i:s");
            $data = $this->mapAndCleanUpdateData($data);
            //print_r($data);
            //exit;
            $numFilasAfectadas = $this->capaDatos->savePgsql($data);
            if($numFilasAfectadas === 0){
                throw new \Exception("No se actualizaron filas. Error en la BD.");
            }
            header('Content-Type: application/json'); 
            http_response_code(200);

            echo json_encode([
                'status' => 'success',
                'message' => 'Datos intraoperatorios actualizados correctamente'
            ]);
            exit;

        }catch(\Exception $e){
            header('Content-Type: application/json'); 
            http_response_code(400); 
            echo json_encode([
                'status' => 'failed',
                'message' => "Error al guardar los datos intraoperatorios " . $e->getMessage()
            ]);
            exit;

        }   
    }

    public function updateDatosIntraOperatorios($data){
        
    }

    /**
     * Mapea y limpia los datos de entrada, asegurando que los tipos sean consistentes.
     * Puedes usar esto para castear valores a FLOAT, INT, etc.
     */
    private function mapAndCleanUpdateData(array $data): array
{
    return [
        'id' => (string)$data['userId'],
        
        // Signos Vitales - Presión Arterial
        'pasistolica_ini' => isset($data['pasistolica_ini']) ? (int)$data['pasistolica_ini'] : null,
        'padiastolica_ini' => isset($data['padiastolica_ini']) ? (int)$data['padiastolica_ini'] : null,
        'pasistolica_postint' => isset($data['pasistolica_postint']) ? (int)$data['pasistolica_postint'] : null,
        'padiastolica_postint' => isset($data['padiastolica_postint']) ? (int)$data['padiastolica_postint'] : null,
        'pasistolica_fin' => isset($data['pasistolica_fin']) ? (int)$data['pasistolica_fin'] : null,
        'padiastolica_fin' => isset($data['padiastolica_fin']) ? (int)$data['padiastolica_fin'] : null,
        
        // Signos Vitales - Frecuencia Cardíaca
        'fcard_ini' => isset($data['fcard_ini']) ? (int)$data['fcard_ini'] : null,
        'fcard_postint' => isset($data['fcard_postint']) ? (int)$data['fcard_postint'] : null,
        'fcard_fin' => isset($data['fcard_fin']) ? (int)$data['fcard_fin'] : null,
        
        // Signos Vitales - Saturación O2
        'sato_ini' => isset($data['sato_ini']) ? (int)$data['sato_ini'] : null,
        'sato_postint' => isset($data['sato_postint']) ? (int)$data['sato_postint'] : null,
        'sato_fin' => isset($data['sato_fin']) ? (int)$data['sato_fin'] : null,
        
        // Otros Signos Vitales
        'etco2' => isset($data['etco2']) ? (float)$data['etco2'] : null,
        'bis' => isset($data['bis']) ? (int)$data['bis'] : null,
        
        // Tiempo Quirúrgico
        'despertar' => isset($data['despertar']) ? (int)$data['despertar'] : null,
        'tiempoqx' => isset($data['tiempoQx']) ? (int)$data['tiempoQx'] : null,
        
        // Fármacos de Inducción
        'induccionpropofol' => isset($data['induccionPropofol']) ? (float)$data['induccionPropofol'] : null,
        'inducciondexmedetomidina' => isset($data['induccionDexmedetomidina']) ? (float)$data['induccionDexmedetomidina'] : null,
        'induccionlidocaina' => isset($data['induccionLidocaina']) ? (float)$data['induccionLidocaina'] : null,
        'induccionketamina' => isset($data['induccionKetamina']) ? (float)$data['induccionKetamina'] : null,
        'induccionrnm' => isset($data['induccionRNM']) ? (float)$data['induccionRNM'] : null,
        
        // Fármacos de Mantenimiento
        'mantenimientosevorane' => isset($data['mantenimientoSevorane']) ? (float)$data['mantenimientoSevorane'] : null,
        'mantenimientodexmedetomidina' => isset($data['mantenimientoDexmedetomidina']) ? (float)$data['mantenimientoDexmedetomidina'] : null,
        'mantenimientolidocaina' => isset($data['mantenimientoLidocaina']) ? (float)$data['mantenimientoLidocaina'] : null,
        'mantenimientoketamina' => isset($data['mantenimientoKetamina']) ? (float)$data['mantenimientoKetamina'] : null,
        'mantenimientosulfatomg' => isset($data['mantenimientoSulfatoMg']) ? (float)$data['mantenimientoSulfatoMg'] : null,
        
        // Coadyuvantes - Checkboxes
        'ondasetron' => (bool)($data['ondasetron'] ?? false),
        'valorondasetron' => isset($data['valorOndasetron']) ? (float)$data['valorOndasetron'] : null,
        
        'metamizol' => (bool)($data['metamizol'] ?? false),
        'valormetamizol' => isset($data['valorMetamizol']) ? (float)$data['valorMetamizol'] : null,
        
        'dexametasona' => (bool)($data['dexametasona'] ?? false),
        'valordexametasona' => isset($data['valorDexametasona']) ? (float)$data['valorDexametasona'] : null,
        
        'ketorol' => (bool)($data['ketorol'] ?? false),
        'valorketorol' => isset($data['valorKetorol']) ? (float)$data['valorKetorol'] : null,
        
        // Timestamps
        'created_at' => $data['created_at'] ?? null
    ];
}
}