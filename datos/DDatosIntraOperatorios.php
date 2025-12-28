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

    public function savePgsql(array $data): bool
    {
        $query = "INSERT INTO datos_intra_operatorios(
            id, 
            pasistolica_ini, padiastolica_ini, pasistolica_postint, padiastolica_postint, pasistolica_fin, padiastolica_fin,
            fcard_ini, fcard_postint, fcard_fin,
            sato_ini, sato_postint, sato_fin,
            etco2, bis,
            despertar, tiempoqx,
            induccionpropofol, inducciondexmedetomidina, induccionlidocaina, induccionketamina, induccionrnm,
            mantenimientosevorane, mantenimientodexmedetomidina, mantenimientolidocaina, mantenimientoketamina, mantenimientosulfatomg,
            ondasetron, valorondasetron, metamizol, valormetamizol, dexametasona, valordexametasona, ketorol, valorketorol,
            created_at
        ) VALUES(
            :id, 
            :pasistolica_ini, :padiastolica_ini, :pasistolica_postint, :padiastolica_postint, :pasistolica_fin, :padiastolica_fin,
            :fcard_ini, :fcard_postint, :fcard_fin,
            :sato_ini, :sato_postint, :sato_fin,
            :etco2, :bis,
            :despertar, :tiempoqx,
            :induccionpropofol, :inducciondexmedetomidina, :induccionlidocaina, :induccionketamina, :induccionrnm,
            :mantenimientosevorane, :mantenimientodexmedetomidina, :mantenimientolidocaina, :mantenimientoketamina, :mantenimientosulfatomg,
            :ondasetron, :valorondasetron, :metamizol, :valormetamizol, :dexametasona, :valordexametasona, :ketorol, :valorketorol,
            :created_at
        )";

        try {
            $stmt = $this->connection->getConnection()->prepare($query);

            // ID
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_STR);
            
            // Signos Vitales - Presión Arterial
            $stmt->bindValue(':pasistolica_ini', $data['pasistolica_ini'], PDO::PARAM_INT);
            $stmt->bindValue(':padiastolica_ini', $data['padiastolica_ini'], PDO::PARAM_INT);
            $stmt->bindValue(':pasistolica_postint', $data['pasistolica_postint'], PDO::PARAM_INT);
            $stmt->bindValue(':padiastolica_postint', $data['padiastolica_postint'], PDO::PARAM_INT);
            $stmt->bindValue(':pasistolica_fin', $data['pasistolica_fin'], PDO::PARAM_INT);
            $stmt->bindValue(':padiastolica_fin', $data['padiastolica_fin'], PDO::PARAM_INT);
            
            // Signos Vitales - Frecuencia Cardíaca
            $stmt->bindValue(':fcard_ini', $data['fcard_ini'], PDO::PARAM_INT);
            $stmt->bindValue(':fcard_postint', $data['fcard_postint'], PDO::PARAM_INT);
            $stmt->bindValue(':fcard_fin', $data['fcard_fin'], PDO::PARAM_INT);
            
            // Signos Vitales - Saturación O2
            $stmt->bindValue(':sato_ini', $data['sato_ini'], PDO::PARAM_INT);
            $stmt->bindValue(':sato_postint', $data['sato_postint'], PDO::PARAM_INT);
            $stmt->bindValue(':sato_fin', $data['sato_fin'], PDO::PARAM_INT);
            
            // Otros Signos Vitales
            $stmt->bindValue(':etco2', $data['etco2'], PDO::PARAM_STR);
            $stmt->bindValue(':bis', $data['bis'], PDO::PARAM_INT);
            
            // Tiempo Quirúrgico
            $stmt->bindValue(':despertar', $data['despertar'], PDO::PARAM_INT);
            $stmt->bindValue(':tiempoqx', $data['tiempoqx'], PDO::PARAM_INT);
            
            // Fármacos de Inducción
            $stmt->bindValue(':induccionpropofol', $data['induccionpropofol'], PDO::PARAM_STR);
            $stmt->bindValue(':inducciondexmedetomidina', $data['inducciondexmedetomidina'], PDO::PARAM_STR);
            $stmt->bindValue(':induccionlidocaina', $data['induccionlidocaina'], PDO::PARAM_STR);
            $stmt->bindValue(':induccionketamina', $data['induccionketamina'], PDO::PARAM_STR);
            $stmt->bindValue(':induccionrnm', $data['induccionrnm'], PDO::PARAM_STR);
            
            // Fármacos de Mantenimiento
            $stmt->bindValue(':mantenimientosevorane', $data['mantenimientosevorane'], PDO::PARAM_STR);
            $stmt->bindValue(':mantenimientodexmedetomidina', $data['mantenimientodexmedetomidina'], PDO::PARAM_STR);
            $stmt->bindValue(':mantenimientolidocaina', $data['mantenimientolidocaina'], PDO::PARAM_STR);
            $stmt->bindValue(':mantenimientoketamina', $data['mantenimientoketamina'], PDO::PARAM_STR);
            $stmt->bindValue(':mantenimientosulfatomg', $data['mantenimientosulfatomg'], PDO::PARAM_STR);
            
            // Coadyuvantes - Ondasetron
            $stmt->bindValue(':ondasetron', $data['ondasetron'], PDO::PARAM_BOOL);
            //$stmt->bindValue(':valorondasetron', $data['valorondasetron'], PDO::PARAM_STR);
            
            // Coadyuvantes - Metamizol
            $stmt->bindValue(':metamizol', $data['metamizol'], PDO::PARAM_BOOL);
            //$stmt->bindValue(':valormetamizol', $data['valormetamizol'], PDO::PARAM_STR);
            
            // Coadyuvantes - Dexametasona
            $stmt->bindValue(':dexametasona', $data['dexametasona'], PDO::PARAM_BOOL);
            //$stmt->bindValue(':valordexametasona', $data['valordexametasona'], PDO::PARAM_STR);
            
            // Coadyuvantes - Ketorol
            $stmt->bindValue(':ketorol', $data['ketorol'], PDO::PARAM_BOOL);
            //$stmt->bindValue(':valorketorol', $data['valorketorol'], PDO::PARAM_STR);
            
            // Timestamps
            $stmt->bindValue(':created_at', $data['created_at'], PDO::PARAM_STR);

            $success = $stmt->execute();
            
            if (!$success) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL: " . print_r($errorInfo, true));
                throw new \Exception("Error al ejecutar la consulta SQL.");
            }
            
            return true;

        } catch (\PDOException $e) {
            error_log("PDO Error en savePgsql(): " . $e->getMessage());
            throw new \Exception("Error de persistencia al guardar datos intraoperatorios: " . $e->getMessage());
        }
    }
}