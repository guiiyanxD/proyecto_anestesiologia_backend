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

    /**
     * Actualizar datos personales existentes
     * 
     * @param array $data Datos a actualizar
     * @return bool True si se actualizó correctamente
     * @throws Exception Si hay error en la actualización
     */
    public function updateDatosPersonales(array $data): bool {
        $this->validateDatosPersonales($data);
        
        // Agregar timestamp de actualización
        //$data['updated_at'] = date('Y-m-d H:i:s');
        
        try {
            $result = $this->datosPersonales->updatePgsql($data);
            
            if (!$result) {
                throw new \Exception("No se pudo actualizar el registro");
            }
            
            return true;
            
        } catch (\Exception $e) {
            error_log("Error en updateDatosPersonales: " . $e->getMessage());
            throw new \Exception("Error al actualizar datos personales: " . $e->getMessage());
        }
    }
    
    /**
     * Validaciones de negocio para datos personales
     * 
     * @param array $data
     * @throws Exception Si alguna validación falla
     */
    private function validateDatosPersonales(array $data): void {
        if (empty($data['id'])) {
            throw new \Exception("ID del paciente es requerido");
        }
        
        if (empty($data['fechanacimiento']) || empty($data['fechacirugia'])) {
            throw new \Exception("Las fechas son requeridas");
        }
        
        $fechaNac = new DateTime($data['fechanacimiento']);
        $fechaCir = new DateTime($data['fechacirugia']);
        
        if ($fechaCir < $fechaNac) {
            throw new \Exception("La fecha de cirugía no puede ser anterior a la fecha de nacimiento");
        }
        
        if (!in_array($data['genero'], ['masculino', 'femenino'])) {
            throw new \Exception("Género inválido");
        }
        
        if ($data['edad'] < 0 || $data['edad'] > 150) {
            throw new \Exception("Edad inválida");
        }
        
        if ($data['peso'] <= 0 || $data['talla'] <= 0) {
            throw new \Exception("Peso y talla deben ser mayores a 0");
        }
        
        if ($data['imc'] <= 0 || $data['imc'] > 100) {
            throw new \Exception("IMC inválido");
        }
        
        if (!in_array($data['asa'], ['Asa I', 'Asa II', 'Asa III'])) {
            throw new \Exception("Clasificación ASA inválida");
        }
        
        $tiposCirugia = ['colecistectomia', 'hernorrafia', 'bariatrica', 'otro'];
        if (!in_array($data['tipocirugia'], $tiposCirugia)) {
            throw new \Exception("Tipo de cirugía inválido");
        }
    }

    
    
}
