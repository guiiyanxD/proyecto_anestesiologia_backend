<?php
require_once('../config/Connection.php');
class DDatosPersonales {
    
    private $connection;

    public function __construct() {
        $this->connection = new Connection();
    }
    
    public function save($data) {
        $query = "INSERT INTO `plenary-glass-470415-k1.second_proy_at.datos_personales` (id, fechaNacimiento, fechaCirugia, genero, peso, talla, imc, asa, tipoCirugia, otraCirugia, edad, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
        $rowData = [
            'id'                => $data['id'],
            'fechaNacimiento'   => $data['fechaNacimiento'],
            'fechaCirugia'      => $data['fechaCirugia'],
            'genero'            => $data['genero'],
            'asa'               => $data['asa'],
            'tipoCirugia'       => $data['tipoCirugia'],
            'otraCirugia'       => $data['otraCirugia'] ?? "",
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

    public function getById($id){
        $query = "SELECT * FROM `plenary-glass-470415-k1.second_proy_at.datos_personales` WHERE id = '".$id."' LIMIT 1";

        try {
            $options = $this->connection->getBigQuery()->query($query);
            $job = $this->connection->getBigQuery()->runQuery($options);
            $result = [];

            $job->waitUntilComplete(); 

            if ($job->isComplete()) {
                foreach ($job as $row) {
                    $result[] = $row;
                }
            }
            
            if (count($result) > 0) {
               $paciente = $result[0];
                return $this->formatBigQueryDates($paciente);
            } else {
                throw new \Exception("No se encontraron datos para el ID proporcionado");
            }
        } catch (\Exception $e) {
            throw new \Exception("Error al obtener los datos de la BD: " . $e->getMessage());
        }
    }

    /**
     * Convierte los objetos Google\Cloud\BigQuery\Date a strings de fecha.
     * @param array $row La fila de resultados de BigQuery.
     * @return array La fila con las fechas formateadas.
     */
    private function formatBigQueryDates(array $row): array
    {
        foreach ($row as $key => $value) {
            
            if ($value instanceof \Google\Cloud\BigQuery\Date || $value instanceof \Google\Cloud\BigQuery\Timestamp) {
                $dateTimeObject = $value->get(); 
                $row[$key] = $dateTimeObject->format('Y-m-d');
            } 
            
        }
        return $row;
    }
        
}

