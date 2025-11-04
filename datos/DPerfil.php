<?php   
require_once('../config/Pgsql.php');
class DPerfil {
    private $connection;
    public function __construct() {
        $this->connection = PgsqlConnection::getInstance();
        $this->connection->getConnection();
    }

    public function getById($id){
        $query = "SELECT 
            p.*, di.*, dp.*
            FROM datos_personales p
            LEFT JOIN datos_intra_operatorios di ON p.id = di.id
            LEFT JOIN datos_post_operatorios dp ON p.id = dp.id
            WHERE p.id = :id;";

        try {
            $stmt = $this->connection->getConnection()->prepare($query);
            
            $stmt->bindValue(':id', $id, PDO::PARAM_STR);
            
            $success = $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$success) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL: " . print_r($errorInfo, true));
                throw new \Exception("Error al ejecutar la consulta SQL.");
            }
            return $result;

        } catch (\Exception $e) {
            throw new \Exception("Error al obtener los datos de la BD: " . $e->getMessage());
        }
    }

    public function getUltimos10(){
        $query = "SELECT *
            FROM datos_personales ORDER BY created_at DESC";

        try {
            $stmt = $this->connection->getConnection()->prepare($query);
            
            
            $success = $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!$success) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL: " . print_r($errorInfo, true));
                throw new \Exception("Error al ejecutar la consulta SQL.");
            }
            return $result;

        } catch (\Exception $e) {
            throw new \Exception("Error al obtener los datos de la BD: " . $e->getMessage());
        }
    }
    
    public function getAll(){
        $query = "SELECT *
            FROM datos_personales ORDER BY created_at DESC";

        try {
            $stmt = $this->connection->getConnection()->prepare($query);
            
            
            $success = $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!$success) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL: " . print_r($errorInfo, true));
                throw new \Exception("Error al ejecutar la consulta SQL.");
            }
            return $result;

        } catch (\Exception $e) {
            throw new \Exception("Error al obtener los datos de la BD: " . $e->getMessage());
        }
    }
}