<?php
require_once('../config/Connection.php');
class DDatosPersonales {
    
    private $connection;

    public function __construct() {
        $this->connection = new Connection();
    }
    
    public function save($data) {
        $query = "INSERT INTO `proyecto-anestesiologia.info_pacietes.datos_pacientes` (id, fechaNacimiento, fechaCirugia, genero, peso, talla, imc, asa, tipoCirugia, otraCirugia, edad, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
        $rowData = [
            'id'                => $data['id'],
            'fechaNacimiento'   => $data['fechaNacimiento'],
            'fechaCirugia'      => $data['fechaCirugia'],
            'genero'            => $data['genero'],
            'asa'               => $data['asa'],
            'tipoCirugia'       => $data['tipoCirugia'],
            'otraCirugia'       => $data['otraCirugia'],
            'edad'              => (int)$data['edad'],
            'imc'               => (float)$data['imc'],
            'peso'              => (float)$data['peso'],
            'talla'             => (float)$data['talla'],
            'created_at'        => $data['created_at']

        ];
        $table = $this->connection->getTable();
        try {
            $response = $table->insertRows([
                ['data' => $rowData]
            ]);
            
            if ($response->isSuccessful()) {
                return true;
            } else {
                $message = "Error de BigQuery al insertar datos";
                $failedRows = $response->failedRows();
                
                throw new \Exception($message);
            }
        } catch (\Exception $e) {
            throw new \Exception("Error al guardar los datos en la BD");
        }
    }
        
}

