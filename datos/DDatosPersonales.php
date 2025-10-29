<?php
require_once('../config/Connection.php');
require_once('../config/Pgsql.php');
class DDatosPersonales {
    
    private $connection;

    public function __construct() {
        $this->connection = PgsqlConnection::getInstance()->getConection();
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


    public function updateIntraOperatorios($data) {

        $query = "UPDATE `plenary-glass-470415-k1.second_proy_at.datos_personales`
            SET 
                isDatosIntraLoaded = ".$data['isDatosIntraLoaded'].",
                induccionPropofol = ".$data['induccionPropofol'].",
                induccionDexmedetomidina = ".$data['induccionDexmedetomidina'].",
                induccionLidocaina = ".$data['induccionLidocaina'].",
                induccionKetamina = ".$data['induccionKetamina'].",
                mantenimientoPropofol = ".$data['mantenimientoPropofol'].",
                mantenimientoDexmedetomidina = ".$data['mantenimientoDexmedetomidina'].",
                mantenimientoLidocaina = ".$data['mantenimientoLidocaina'].",
                mantenimientoKetamina = ".$data['mantenimientoKetamina'].",
                despertar = ".$data['despertar'].",
                tiempoQx = ".$data['tiempoQx'].",
                presionArterial = ".$data['presionArterial'].",
                valorPresionArterial = ".$data['valorPresionArterial'].",
                frecuenciaCardiaca = ".$data['frecuenciaCardiaca'].",
                valorFrecuenciaCardiaca = ".$data['valorFrecuenciaCardiaca'].",
                frecuenciaRespiratoria = ".$data['frecuenciaRespiratoria'].",
                valorFrecuenciaRespiratoria = ".$data['valorFrecuenciaRespiratoria'].",
                co2 = ".$data['co2'].",
                valorCo2 = ".$data['valorCo2'].",
                satO2 = ".$data['satO2']." ,
                valorSatO2 = ".$data['valorSatO2']." WHERE id = ".$data['userId']. ";";

        
        $bigQuery = $this->connection->getBigQuery();
        try {
            $job = $bigQuery->runQuery(
                $bigQuery->query($query),
                [
                    'configuration' => [
                        'query' => [
                            'useLegacySql' => false
                        ]
                    ]
                ]);
            $job->waitUntilComplete();
            if ($job->isComplete()) {
                return $job->info()["numDmlAffectedRows"];
            } else {
                $error = $job->info()['status']['errorResult'] ?? 'Unknown error';
                throw new \Exception('BigQuery job failed: '  . json_encode($error));
            }
        } catch (\Exception $e) {
            throw new \Exception(print_r($query, true) ."\n".  $e->getMessage());
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
        
        // 1. La consulta SQL con marcadores de posición (placeholders) para seguridad
        $sql = "INSERT INTO pacientes_cirugia (
            id, \"fechaNacimiento\", \"fechaCirugia\", genero, asa, \"tipoCirugia\", 
            \"otraCirugia\", edad, imc, peso, talla, created_at
        ) VALUES (
            :id, :fechaNacimiento, :fechaCirugia, :genero, :asa, :tipoCirugia, 
            :otraCirugia, :edad, :imc, :peso, :talla, :created_at
        )";

        try {
            // 2. Preparar la declaración
            $stmt = $this->connection->prepare($sql);

            // 3. Unir los parámetros (Binding)
            // Se usa bindValue con el tipo de dato explícito de PDO para mayor seguridad
            
            // Strings
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_STR);
            $stmt->bindValue(':genero', $data['genero'], PDO::PARAM_STR);
            $stmt->bindValue(':asa', $data['asa'], PDO::PARAM_STR);
            $stmt->bindValue(':tipoCirugia', $data['tipoCirugia'], PDO::PARAM_STR);
            $stmt->bindValue(':otraCirugia', $data['otraCirugia'] ?? "", PDO::PARAM_STR);
            
            // Fechas (PostgreSQL las acepta como STR)
            $stmt->bindValue(':fechaNacimiento', $data['fechaNacimiento'], PDO::PARAM_STR);
            $stmt->bindValue(':fechaCirugia', $data['fechaCirugia'], PDO::PARAM_STR);
            $stmt->bindValue(':created_at', $data['created_at'], PDO::PARAM_STR);

            // Entero
            $stmt->bindValue(':edad', (int)$data['edad'], PDO::PARAM_INT);
            
            // Flotantes/Numéricos (PostgreSQL los acepta como STR o puedes usar PARAM_STR)
            $stmt->bindValue(':imc', (float)$data['imc'], PDO::PARAM_STR);
            $stmt->bindValue(':peso', (float)$data['peso'], PDO::PARAM_STR);
            $stmt->bindValue(':talla', (float)$data['talla'], PDO::PARAM_STR);

            // 4. Ejecutar la declaración
            $success = $stmt->execute();
            
            if (!$success) {
                 // Puedes obtener información de error si la ejecución falla
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL: " . print_r($errorInfo, true));
                throw new \Exception("Error al ejecutar la consulta SQL.");
            }

            return true;

        } catch (\PDOException $e) {
            // Manejar errores de PDO (conexión, sintaxis, etc.)
            error_log("PDO Error en save(): " . $e->getMessage());
            throw new \Exception("Error de persistencia al guardar el paciente.");
        }
    }  
}

