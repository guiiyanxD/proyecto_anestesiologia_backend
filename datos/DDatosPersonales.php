<?php
require_once('../config/Connection.php');
require_once('../config/Pgsql.php');
class DDatosPersonales {
    
    private $connection;

    public function __construct() {
        $this->connection = PgsqlConnection::getInstance();
        $this->connection->getConnection();
    }
    
    

    public function getById($id){
        $query = "SELECT * FROM datos_personales WHERE id = :id";

        try {
            $stmt = $this->connection->getConnection()->prepare($query);
            
            $stmt->bindValue(':id', $id, PDO::PARAM_STR);
            
            $success = $stmt->execute();
   
            if (!$success) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL: " . print_r($errorInfo, true));
                throw new \Exception("Error al ejecutar la consulta SQL.");
            }
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            throw new \Exception("Error al obtener los datos de la BD: " . $e->getMessage());
        }
    }

    /**
     * Inserta un nuevo registro en la tabla 'pacientes_cirugia'.
     *
     * @param array $data Array asociativo con los datos del paciente.
     * @return bool Retorna true si la inserción fue exitosa.
     * @throws \Exception Si la inserción falla.
     */
    public function savePgsql(array $data): bool {
        
        $sql = "INSERT INTO datos_personales (
            id, fechanacimiento, fechacirugia, genero, asa, tipocirugia, 
            otracirugia, edad, imc, peso, talla, created_at
        ) VALUES (
            :id, :fechanacimiento, :fechacirugia, :genero, :asa, :tipocirugia, 
            :otracirugia, :edad, :imc, :peso, :talla, :created_at
        )";

        try {
           
            $stmt = $this->connection->getConnection()->prepare($sql);

            $stmt->bindValue(':id', $data['id'], PDO::PARAM_STR);
            $stmt->bindValue(':genero', $data['genero'], PDO::PARAM_STR);
            $stmt->bindValue(':asa', $data['asa'], PDO::PARAM_STR);
            $stmt->bindValue(':tipocirugia', $data['tipocirugia'], PDO::PARAM_STR);
            $stmt->bindValue(':otracirugia', $data['otracirugia'] ?? "", PDO::PARAM_STR);
            $stmt->bindValue(':fechanacimiento', $data['fechanacimiento'], PDO::PARAM_STR);
            $stmt->bindValue(':fechacirugia', $data['fechacirugia'], PDO::PARAM_STR);
            $stmt->bindValue(':created_at', $data['created_at'], PDO::PARAM_STR);
            $stmt->bindValue(':edad', (int)$data['edad'], PDO::PARAM_INT);
            $stmt->bindValue(':imc', (float)$data['imc'], PDO::PARAM_STR);
            $stmt->bindValue(':peso', (float)$data['peso'], PDO::PARAM_STR);
            $stmt->bindValue(':talla', (float)$data['talla'], PDO::PARAM_STR);

            $success = $stmt->execute();
            //print_r($success);
            //exit;
            if (!$success) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL: " . print_r($errorInfo, true));
                throw new \Exception("Error al ejecutar la consulta SQL.");
            }
            return true;

        } catch (\PDOException $e) {
            error_log("PDO Error en save(): " . $e->getMessage());
            throw new \Exception("Error de persistencia al guardar el paciente." . $e->getMessage());
        }
    }  
}

