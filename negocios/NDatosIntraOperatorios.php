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
                'message' => "Error al actualizar los datos intraoperatorios " . $e->getMessage()
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
            'id'                            => (string)$data['userId'],
            'induccionpropofol'             => (float)$data['induccionPropofol'],
            'inducciondexmedetomidina'      => (float)$data['induccionDexmedetomidina'],
            'induccionlidocaina'            => (float)$data['induccionLidocaina'],
            'induccionketamina'             => (float)$data['induccionKetamina'],
            'mantenimientopropofol'         => (float)$data['mantenimientoPropofol'],
            'mantenimientodexmedetomidina'  => (float)$data['mantenimientoDexmedetomidina'],
            'mantenimientolidocaina'        => (float)$data['mantenimientoLidocaina'],
            'mantenimientoketamina'         => (float)$data['mantenimientoKetamina'],
            'despertar'                     => (int)$data['despertar'],
            'presionarterial'               => (bool)$data['presionArterial'] ?? false,
            'frecuenciacardiaca'            => (bool)$data['frecuenciaCardiaca'] ?? false,
            'frecuenciarespiratoria'        => (bool)$data['frecuenciaRespiratoria'] ?? false,
            'co2'                           => (bool)$data['co2'] ?? false,
            'sato2'                         => (bool)$data['satO2'] ?? false,
            'tiempoqx'                      => (int)($data['tiempoQx']),
            'valorpresionarterial'          => (String)($data['valorPresionArterial'] ?? "NO"),
            'valorfrecuenciacardiaca'       => (String)($data['valorFrecuenciaCardiaca'] ?? "NO"),
            'valorfrecuenciarespiratoria'   => (String)($data['valorFrecuenciaRespiratoria'] ?? "NO"),
            'valorsato2'                    => (String)($data['valorSatO2'] ?? "NO"),
            'valorco2'                      => (String)($data['valorCo2'] ?? "NO"),
            'created_at'                    => $data['created_at']

        ];
    }
}