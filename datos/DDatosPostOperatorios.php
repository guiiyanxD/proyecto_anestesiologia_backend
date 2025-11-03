<?php
require_once('../config/Pgsql.php');
class DDatosPostOperatorios {
    private $connection;

    public function __construct(){
        $this->connection = PgsqlConnection::getInstance();
        $this->connection->getConnection();
    }

    public function insertPostOperatorios(array $data) {
        $rowData =($data);
        $table = $this->connection->getDataset()->table('datos_post_operatorios');
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
            throw new \Exception("Error al insertar los datos post-operatorios: " . $e->getMessage());
        }
    }

    public function savePgsql(array $data) : bool {
        $query = "INSERT INTO datos_post_operatorios(
        id, recuperacionpostanestesia, ramsay, evaingreso, eva1hr,
        nauseas, vomitos, consumoanalgesico, tipoanalgesico,
        depresionrespiratoria, spo2bajo, created_at
        ) VALUES(
        :id, :recuperacionpostanestesia, :ramsay, :evaingreso, :eva1hr,
        :nauseas, :vomitos, :consumoanalgesico, :tipoanalgesico,
        :depresionrespiratoria, :spo2bajo, :created_at
        )";

        try {
           
            $stmt = $this->connection->getConnection()->prepare($query);

            $stmt->bindValue(':id', $data['id'], PDO::PARAM_STR);
            
            $stmt->bindValue(':recuperacionpostanestesia', $data['recuperacionpostanestesia'], PDO::PARAM_INT);
            $stmt->bindValue(':ramsay', $data['ramsay'], PDO::PARAM_INT);
            $stmt->bindValue(':evaingreso', $data['evaingreso'], PDO::PARAM_INT);
            $stmt->bindValue(':eva1hr', $data['eva1hr'], PDO::PARAM_INT);

            $stmt->bindValue(':nauseas', $data['nauseas'], PDO::PARAM_BOOL);
            $stmt->bindValue(':vomitos', $data['vomitos'], PDO::PARAM_BOOL);
            $stmt->bindValue(':consumoanalgesico', $data['consumoanalgesico'], PDO::PARAM_BOOL);
            $stmt->bindValue(':depresionrespiratoria', $data['depresionrespiratoria'], PDO::PARAM_BOOL);

            $stmt->bindValue(':tipoanalgesico', $data['tipoanalgesico'], PDO::PARAM_STR);
            $stmt->bindValue(':spo2bajo', $data['spo2bajo'], PDO::PARAM_STR);
          
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