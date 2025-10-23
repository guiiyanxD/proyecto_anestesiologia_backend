<?php
require_once("../datos/DDatosPersonales.php");
class NDatosPersonales {

    private $datosPersonales;

    public function __construct() {
        $this->datosPersonales = new DDatosPersonales();
    }
    public function saveDatosPersonales($data) {
        try{
            $id = uniqid();
            $data['id'] = $id;
            $data['created_at'] = date("Y-m-d");
            $this->datosPersonales->save($data);

            header('Content-Type: application/json'); 
            http_response_code(200);

            echo json_encode([
                'status' => 'success',
                'message' => 'Datos personales guardados correctamente',
                'data' => $id
            ]);
            exit;

        }catch(\Exception $e){
            header('Content-Type: application/json'); 
            http_response_code(400); 
            echo json_encode([
                'status' => 'failed',
                'message' => 'Error al guardar los datos personales: ' . $e->getMessage()
            ]);
            exit;

        }    
    }

    public function getUserData($data) {
        try{
            $userId = $data['id'];
            $userData = $this->datosPersonales->getById($userId);

            header('Content-Type: application/json'); 
            http_response_code(200);

            echo json_encode([
                'status' => 'success',
                'data' => $userData
            ]);
            exit;

        }catch(\Exception $e){
            header('Content-Type: application/json'); 
            http_response_code(400); 
            echo json_encode([
                'status' => 'failed',
                'message' => "Error al obtener los datos del usuario con ID: " . $data['id'] . " " . $e->getMessage()
            ]);
            exit;

        }    
    }

    public function updateDatosIntraOperatorios($data) {
        try{
            $data = $this->mapAndCleanUpdateData($data);
            $numFilasAfectadas = $this->datosPersonales->updateIntraOperatorios( $data);
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
            'userId'                      => (string)$data['userId'],
            'isDatosIntraLoaded'          => (bool)1,
            'induccionPropofol'           => (int)$data['induccionPropofol'],
            'induccionDexmedetomidina'    => (int)$data['induccionDexmedetomidina'],
            'induccionLidocaina'          => (int)$data['induccionLidocaina'],
            'induccionKetamina'           => (int)$data['induccionKetamina'],
            'mantenimientoPropofol'       => (int)$data['mantenimientoPropofol'],
            'mantenimientoDexmedetomidina' => (int)$data['mantenimientoDexmedetomidina'],
            'mantenimientoLidocaina'      => (int)$data['mantenimientoLidocaina'],
            'mantenimientoKetamina'       => (int)$data['mantenimientoKetamina'],
            'despertar'                   => (int)$data['despertar'],
            'presionArterial'             => (bool)$data['presionArterial'] ?? false,
            'frecuenciaCardiaca'          => (bool)$data['frecuenciaCardiaca'] ?? false,
            'frecuenciaRespiratoria'      => (bool)$data['frecuenciaRespiratoria'] ?? false,
            'co2'                         => (bool)$data['co2'] ?? false,
            'satO2'                       => (bool)$data['satO2'] ?? false,
            'tiempoQx'                    => (int)($data['tiempoQx']),
            'valorPresionArterial'        => (String)($data['valorPresionArterial'] ?? "NO"),
            'valorFrecuenciaCardiaca'     => (String)($data['valorFrecuenciaCardiaca'] ?? "NO"),
            'valorFrecuenciaRespiratoria' => (String)($data['valorFrecuenciaRespiratoria'] ?? "NO"),
            'valorSatO2'                  => (String)($data['valorSatO2'] ?? "NO"),
            'valorCo2'                    => (String)($data['valorCo2'] ?? "NO")

        ];
    }
}
