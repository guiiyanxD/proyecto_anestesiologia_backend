<?php
require_once('../config/Connection.php');
class DDatosPostOperatorios {
    private $connection;

    public function __construct(){
        $this->connection = new Connection();
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
}