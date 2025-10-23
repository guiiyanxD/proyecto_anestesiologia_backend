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


    /*public function updateIntraOperatorios($data) {
        
        // 1. Separar el ID (la clave de la condición WHERE)
        $userId = $data['userId'];
        //echo $userId;
        //unset($data['userId']); // Remover el ID del array de campos a actualizar
        
        // 2. Mapear y limpiar los datos (esto es clave para la seguridad y el formato)
        $paramsToUpdate = $this->mapAndCleanUpdateData($data);
        
        // 3. Generar la cláusula SET dinámicamente
        // Ejemplo: SET columna1 = @columna1, columna2 = @columna2, ...
        $setClauses = [];
        $parameters = [];
        
        foreach ($paramsToUpdate as $column => $value) {
            $setClauses[] = "`{$column}` = @{$column}";
            
            // BigQuery necesita el tipo y el valor para la parametrización
            // Nota: Aquí se asume que todos son STRING para simplificar, 
            // pero deberías ajustar el tipo ('STRING', 'FLOAT', 'BOOL', etc.) 
            // si quieres mayor precisión.
            $parameters[] = [
                'name' => $column,
                'type' => $this->getBigQueryDataType($column), // Usa la función de mapeo de tipo
                'value' => $value,
            ];
        }

        // 4. Agregar el ID para la cláusula WHERE
        $whereClause = "id = @userId";
        $parameters[] = [
            'name' => 'userId',
            'type' => 'STRING', // Asume que el ID es STRING
            'value' => $userId,
        ];
        $where = "id = '". $userId. "';" ;
        // 5. Construir la consulta SQL final
        $setSql = implode(",\n\t\t\t\t", $setClauses);
        $query = "UPDATE `plenary-glass-470415-k1.second_proy_at.datos_personales` 
            SET {$setSql} 
            WHERE {$where} ";
        /*echo $query;
        exit;
        // Ejecución
        $bigQuery = $this->connection->getBigQuery();
        try {
            $queryConfig = $bigQuery->query($query, [
                'parameters' => $parameters, // ⭐️ Pasamos el array de parámetros nombrados
                'configuration' => [
                        'query' => [
                            'useLegacySql' => false
                        ]
                    ]
            ]);

            $job = $bigQuery->runQuery($queryConfig);
            
            $job->waitUntilComplete();
            
            if ($job->isComplete()) {
                // BigQuery devuelve el número de filas afectadas en los resultados DML
                return $true ;
            } else {
                $error = $job->info()['status']['errorResult'] ?? 'Unknown error';
                throw new \Exception('BigQuery job failed: ' . json_encode($error));
            }
        } catch (\Exception $e) {
            throw new \Exception("Error al actualizar los datos en la BD: " . print_r($userId, true) . $e->getMessage());
        }
    }*/
    
    /**
     * Mapea y limpia los datos de entrada, asegurando que los tipos sean consistentes.
     * Puedes usar esto para castear valores a FLOAT, INT, etc.
     */
    /*private function mapAndCleanUpdateData(array $data): array
    {
        return [
            // Cadenas de texto
            'isDatosIntraLoaded'          => (bool)true,
            'induccionPropofol'           => (int)$data['induccionPropofol'],
            'induccionDexmedetomidina'    => (int)$data['induccionDexmedetomidina'],
            'induccionLidocaina'          => (int)$data['induccionLidocaina'],
            'induccionKetamina'           => (int)$data['induccionKetamina'],
            'mantenimientoPropofol'       => (int)$data['mantenimientoPropofol'],
            'mantenimientoDexmedetomidina' => (int)$data['mantenimientoDexmedetomidina'],
            'mantenimientoLidocaina'      => (int)$data['mantenimientoLidocaina'],
            'mantenimientoKetamina'       => (int)$data['mantenimientoKetamina'],
            'despertar'                   => (int)$data['despertar'],
            'presionArterial'             => (bool)$data['presionArterial'],
            'frecuenciaCardiaca'          => (bool)$data['frecuenciaCardiaca'],
            'frecuenciaRespiratoria'      => (bool)$data['frecuenciaRespiratoria'],
            'co2'                         => (bool)$data['co2'],
            'satO2'                       => (bool)$data['satO2'],

            // Valores numéricos (castéalos para mayor seguridad)
            'tiempoQx'                    => (int)($data['tiempoQx'] ?? 0),
            'valorPresionArterial'        => (String)($data['valorPresionArterial'] ?? " "),
            'valorFrecuenciaCardiaca'     => (String)($data['valorFrecuenciaCardiaca'] ?? ""),
            'valorFrecuenciaRespiratoria' => (String)($data['valorFrecuenciaRespiratoria'] ?? ""),
            'valorSatO2'                  => (String)($data['valorSatO2'] ?? ""),
            'valorCo2'                    => (String)($data['valorCo2'] ?? "")
            
        ];
    }*/
    
    /**
     * Devuelve el tipo de dato de BigQuery para una columna específica.
     */
    /*private function getBigQueryDataType(string $column): string
    {
        // Define aquí los tipos que no son STRING
        $numericTypes = [
            'induccionPropofol', 'induccionDexmedetomidina', 'induccionLidocaina', 'induccionKetamina',
            'mantenimientoPropofol', 'mantenimientoDexmedetomidina', 'mantenimientoLidocaina', 'mantenimientoKetamina',
            'despertar', 'tiempoQx'
        ];
        
        $boolTypes = [
            'presionArterial', 'frecuenciaCardiaca', 'frecuenciaRespiratoria', 
            'co2', 'satO2', 'isDatosIntraLoaded'
        ];

         if (in_array($column, $boolTypes)) {
            return 'BOOL';
        }

        if (in_array($column, $numericTypes)) {
            return 'INT64';
        }
        
        return 'STRING';
    }*/
        
}

