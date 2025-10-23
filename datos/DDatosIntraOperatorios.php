<?php
require_once('../config/Connection.php');
class DDatosIntraOperatorios{
    private $connection;

    public function __construct(){
        $this->connection = new Connection();
    }

    public function saveIntraOperatorios(){
        return true;
    }

    public function insertIntraOperatorios(array $data) {
        
        // 1. Mapear y limpiar los datos de entrada
        $rowData =($data);
        
        // 2. Obtener la conexión a la tabla específica
        // NOTA: Debes asegurarte de que tu clase Connection pueda cambiar la tabla o
        // debes usar $this->connection->getDataset()->table('datos_intra_operatorios')
        $table = $this->connection->getDataset()->table('datos_intra_operatorios');

        try {
            // 3. Insertar la fila. BigQuery requiere un array de arrays
            $response = $table->insertRows([
                ['data' => $rowData]
            ]);
            
            if ($response->isSuccessful()) {
                return true;
            } else {
                // Manejo de errores detallado de BigQuery
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

}