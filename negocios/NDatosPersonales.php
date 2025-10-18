<?php
require_once("../datos/DDatosPersonales.php");
class NDatosPersonales {

    private $datosPersonales;

    public function __construct() {
        $this->datosPersonales = new DDatosPersonales();
    }
    public function saveDatosPersonales($data) {
       $id = uniqid();
       $data['id'] = $id;
       $resultado = $this->datosPersonales->save($data);
       if($resultado)  {
           http_response_code(200);
           echo json_encode([
               'status' => 'success',
               'message' => 'Datos personales guardados correctamente',
               'data' => $id
           ]);
           exit;
       } else {
            http_response_code(200);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al guardar los datos personales'
            ]);
            exit;
       }
    }
}
