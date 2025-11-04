<?php
require_once("../datos/DPerfil.php");

class NPerfil {
    private $capaDatos;
    public function __construct() {
        $this->capaDatos = new DPerfil();
    }

    public function getUserData($data) {
        try{
            $userId = $data['id'];
            $userData = $this->capaDatos->getById($userId);
           
            /*print_r("se obtuvo esto: \n");
            print_r($userData);
            exit();
            */

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

    private function mapAndCleanUpdateData(array $data): array
    {
        return [
            'id'=> (string)$data['userId'],
        ];
    }

    public function getUltimos10() {
        try {
            $registros = $this->capaDatos->getUltimos10();
            
            header('Content-Type: application/json');
            http_response_code(200);

            echo json_encode([
                'status' => 'success',
                'data' => $registros
            ]);
            exit;
        } catch(\Exception $e) {
            // manejo de error
        }
    }


    public function getTodosRegistros() {
        try {
            $registros = $this->capaDatos->getAll();
            
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $registros
            ]);
            exit;
        } catch(\Exception $e) {
            // manejo de error
        }
    }
}