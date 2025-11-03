<?php

require_once('../config/Pgsql.php');
class DDatosIntraOperatorios{
    private $connection;

    public function __construct(){
        $this->connection = PgsqlConnection::getInstance();
        $this->connection->getConnection();
    }

    public function saveIntraOperatorios(){
        return true;
    }

    public function insertIntraOperatorios(array $data) {
        $rowData =($data);
        $table = $this->connection->getDataset()->table('datos_intra_operatorios');
        try {
            $response = $table->insertRows([
                ['data' => $rowData]
            ]);
            if ($response->isSuccessful()) {
                return true;
            } else {
                $failedRows = $response->failedRows();
                $message = "Error en la inserción de BigQuery.";
                if (!empty($failedRows)) {
                    foreach ($failedRows as $failedRow) {
                        foreach ($failedRow['errors'] as $error) {
                            $message .= " Detalle: {$error['reason']}: {$error['message']}\n";
                        }
                    }
                }
                throw new \Exception($message);
            }
            
        } catch (\Exception $e) {
            throw new \Exception("Error al insertar los datos intra-operatorios: " . $e->getMessage());
        }
    }

    public function  savePgsql(array $data): bool{
        $query = "INSERT INTO datos_intra_operatorios(
        id, induccionpropofol, inducciondexmedetomidina, induccionketamina, induccionlidocaina,
        mantenimientopropofol, mantenimientodexmedetomidina, mantenimientoketamina, mantenimientolidocaina,
        despertar, tiempoqx, presionarterial, frecuenciacardiaca, frecuenciarespiratoria, co2, sato2,
        valorpresionarterial, valorfrecuenciacardiaca, valorfrecuenciarespiratoria, valorco2, valorsato2, created_at
        ) VALUES(
        :id, :induccionpropofol, :inducciondexmedetomidina, :induccionketamina, :induccionlidocaina,
        :mantenimientopropofol, :mantenimientodexmedetomidina, :mantenimientoketamina, :mantenimientolidocaina,
        :despertar, :tiempoqx, :presionarterial, :frecuenciacardiaca, :frecuenciarespiratoria, :co2, :sato2,
        :valorpresionarterial, :valorfrecuenciacardiaca, :valorfrecuenciarespiratoria, :valorco2, :valorsato2, :created_at
        )";

        try {
           
            $stmt = $this->connection->getConnection()->prepare($query);

            $stmt->bindValue(':id', $data['id'], PDO::PARAM_STR);
            
            $stmt->bindValue(':induccionpropofol', $data['induccionpropofol'], PDO::PARAM_STR);
            $stmt->bindValue(':inducciondexmedetomidina', $data['inducciondexmedetomidina'], PDO::PARAM_STR);
            $stmt->bindValue(':induccionketamina', $data['induccionketamina'], PDO::PARAM_STR);
            $stmt->bindValue(':induccionlidocaina', $data['induccionlidocaina'], PDO::PARAM_STR);
            
            $stmt->bindValue(':mantenimientopropofol', $data['mantenimientopropofol'], PDO::PARAM_STR);
            $stmt->bindValue(':mantenimientodexmedetomidina', $data['mantenimientodexmedetomidina'], PDO::PARAM_STR);
            $stmt->bindValue(':mantenimientoketamina', $data['mantenimientoketamina'], PDO::PARAM_STR);
            $stmt->bindValue(':mantenimientolidocaina', $data['mantenimientolidocaina'], PDO::PARAM_STR);
            
            $stmt->bindValue(':tiempoqx', (int)$data['tiempoqx'], PDO::PARAM_INT);
            $stmt->bindValue(':despertar', (int)$data['despertar'], PDO::PARAM_INT);

            $stmt->bindValue(':presionarterial', $data['presionarterial'], PDO::PARAM_BOOL);
            $stmt->bindValue(':frecuenciacardiaca', $data['frecuenciacardiaca'], PDO::PARAM_BOOL);
            $stmt->bindValue(':frecuenciarespiratoria', $data['frecuenciarespiratoria'], PDO::PARAM_BOOL);
            $stmt->bindValue(':co2', $data['co2'], PDO::PARAM_BOOL);
            $stmt->bindValue(':sato2', $data['sato2'], PDO::PARAM_BOOL);

            $stmt->bindValue(':valorpresionarterial', $data['valorpresionarterial'], PDO::PARAM_STR);
            $stmt->bindValue(':valorfrecuenciacardiaca', $data['valorfrecuenciacardiaca'], PDO::PARAM_STR);
            $stmt->bindValue(':valorfrecuenciarespiratoria', $data['valorfrecuenciarespiratoria'], PDO::PARAM_STR);
            $stmt->bindValue(':valorco2', $data['valorco2'], PDO::PARAM_STR);
            $stmt->bindValue(':valorsato2', $data['valorsato2'], PDO::PARAM_STR);

            $stmt->bindValue(':created_at', $data['created_at'], PDO::PARAM_STR);


            $success = $stmt->execute();
            
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