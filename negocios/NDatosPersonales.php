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
            $data['created_at'] = date("Y-m-d H:i:s");
            //print_r($data);
            //exit;
            $this->datosPersonales->savePgsql($data);

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

    
    
}
