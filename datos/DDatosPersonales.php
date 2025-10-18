<?php
require_once('../config/Connection.php');
class DDatosPersonales {
    
    private $connection;

    public function __construct() {
        $this->connection = new Connection();
    }
    
    public function save($data) {
        $query = "INSERT INTO `proyecto-anestesiologia.info_pacietes.datos_pacientes` (id) VALUES (?)";
        $rowData = [
            'id' => $data['id']
        ];
        $table = $this->connection->getTable();
        try {
            // Insertar con formato correcto
            $response = $table->insertRows([
                ['data' => $rowData]
            ]);
            
            if ($response->isSuccessful()) {
                return true;
            } else {
                $message = "";
                
                // Debug detallado
                $failedRows = $response->failedRows();
                if (!empty($failedRows)) {
                    foreach ($failedRows as $index => $failedRow) {
                        $message .=   "Fila {$index} falló:\n";
                        foreach ($failedRow['errors'] as $error) {
                            $message .= "  - {$error['reason']}: {$error['message']}\n";
                        }
                    }
                }

                return false;
            }
            
        } catch (\Google\Cloud\Core\Exception\GoogleException $e) {
            echo " Google Exception: " . $e->getMessage() . "\n";
            return false;
        } catch (Exception $e) {
            echo " General Exception: " . $e->getMessage() . "\n";
            return false;
        }
    }
        
}

