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
            //header('Content-Type: application/json'); 
            http_response_code(400); 
            echo json_encode([
                'status' => 'failed',
                'message' => "Error al guardar los datos intraoperatorios " . $e->getMessage()
            ]);
            exit;

        }   
    }

    public function updateDatosIntraOperatorios($data){
        try{
            //$data['created_at'] = date("Y-m-d H:i:s");
            $data = $this->mapAndCleanUpdateData($data);
            //echo json_encode($data);
            //exit;
            $numFilasAfectadas = $this->capaDatos->updatePgsql($data);
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

    /**
     * Mapea y limpia los datos de entrada, asegurando que los tipos sean consistentes.
     * Puedes usar esto para castear valores a FLOAT, INT, etc.
     */
    private function mapAndCleanUpdateData(array $data): array
    {
        return [
            'id' => (string)$data['userId'],
            
            // Signos Vitales - Presión Arterial
            'pasistolica_ini' => (int)$data['pasistolica_ini'],
            'padiastolica_ini' => (int)$data['padiastolica_ini'],
            'pasistolica_postint' => (int)$data['pasistolica_postint'],
            'padiastolica_postint' => (int)$data['padiastolica_postint'],
            'pasistolica_fin' => (int)$data['pasistolica_fin'],
            'padiastolica_fin' => (int)$data['padiastolica_fin'],
            
            // Signos Vitales - Frecuencia Cardíaca
            'fcard_ini' => (int)$data['fcard_ini'],
            'fcard_postint' => (int)$data['fcard_postint'],
            'fcard_fin' => (int)$data['fcard_fin'],
            
            // Signos Vitales - Saturación O2
            'sato_ini' => (int)$data['sato_ini'],
            'sato_postint' => (int)$data['sato_postint'],
            'sato_fin' => (int)$data['sato_fin'],
            
            // Otros Signos Vitales
            'etco2' => (float)$data['etco2'],
            'bis' => (int)$data['bis'],
            
            // Tiempo Quirúrgico
            'despertar' => (int)$data['despertar'],
            'tiempoqx' => (int)$data['tiempoQx'],
            
            // Fármacos de Inducción (checkboxes)
            'induccionpropofol' => (bool)($data['induccionPropofol'] ?? false),
            'inducciondexmedetomidina' => (bool)($data['induccionDexmedetomidina'] ?? false),
            'induccionlidocaina' => (bool)($data['induccionLidocaina'] ?? false),
            'induccionketamina' => (bool)($data['induccionKetamina'] ?? false),
            'induccionrnm' => (bool)($data['induccionRNM'] ?? false),
            
            // Fármacos de Mantenimiento (checkboxes)
            'mantenimientosevorane' => (bool)($data['mantenimientoSevorane'] ?? false),
            'mantenimientodexmedetomidina' => (bool)($data['mantenimientoDexmedetomidina'] ?? false),
            'mantenimientolidocaina' => (bool)($data['mantenimientoLidocaina'] ?? false),
            'mantenimientoketamina' => (bool)($data['mantenimientoKetamina'] ?? false),
            'mantenimientosulfatomg' => (bool)($data['mantenimientoSulfatoMg'] ?? false),
            
            // Coadyuvantes (checkboxes)
            'ondasetron' => (bool)($data['ondasetron'] ?? false),
            'metamizol' => (bool)($data['metamizol'] ?? false),
            'dexametasona' => (bool)($data['dexametasona'] ?? false),
            'ketorol' => (bool)($data['ketorol'] ?? false),
            
            // Timestamps
            //'created_at' => $data['created_at'] ?? null
        ];
    }
}