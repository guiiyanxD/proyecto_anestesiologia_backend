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
}
