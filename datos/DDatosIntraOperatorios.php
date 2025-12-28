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
            ondasetron, metamizol, dexametasona, ketorol,
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
            :ondasetron, :metamizol, :dexametasona, :ketorol,
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
            
            // Fármacos de Inducción (checkboxes)
            $stmt->bindValue(':induccionpropofol', (int)$data['induccionpropofol'], PDO::PARAM_INT);
            $stmt->bindValue(':inducciondexmedetomidina', (int)$data['inducciondexmedetomidina'], PDO::PARAM_INT);
            $stmt->bindValue(':induccionlidocaina', (int)$data['induccionlidocaina'], PDO::PARAM_INT);
            $stmt->bindValue(':induccionketamina', (int)$data['induccionketamina'], PDO::PARAM_INT);
            $stmt->bindValue(':induccionrnm', (int)$data['induccionrnm'], PDO::PARAM_INT);
            
            // Fármacos de Mantenimiento (checkboxes)
            $stmt->bindValue(':mantenimientosevorane', (int)$data['mantenimientosevorane'], PDO::PARAM_INT);
            $stmt->bindValue(':mantenimientodexmedetomidina', (int)$data['mantenimientodexmedetomidina'], PDO::PARAM_INT);
            $stmt->bindValue(':mantenimientolidocaina', (int)$data['mantenimientolidocaina'], PDO::PARAM_INT);
            $stmt->bindValue(':mantenimientoketamina', (int)$data['mantenimientoketamina'], PDO::PARAM_INT);
            $stmt->bindValue(':mantenimientosulfatomg', (int)$data['mantenimientosulfatomg'], PDO::PARAM_INT);
                
            // Coadyuvantes (checkboxes)
            $stmt->bindValue(':ondasetron', (int)$data['ondasetron'], PDO::PARAM_INT);
            $stmt->bindValue(':metamizol', (int)$data['metamizol'], PDO::PARAM_INT);
            $stmt->bindValue(':dexametasona', (int)$data['dexametasona'], PDO::PARAM_INT);
            $stmt->bindValue(':ketorol', (int)$data['ketorol'], PDO::PARAM_INT);
            
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
            throw new \Exception("-- " . $e->getMessage());
        }
    }

    public function updatePgsql(array $data): bool
    {
        $query = "UPDATE datos_intra_operatorios SET
            pasistolica_ini = :pasistolica_ini,
            padiastolica_ini = :padiastolica_ini,
            pasistolica_postint = :pasistolica_postint,
            padiastolica_postint = :padiastolica_postint,
            pasistolica_fin = :pasistolica_fin,
            padiastolica_fin = :padiastolica_fin,
            fcard_ini = :fcard_ini,
            fcard_postint = :fcard_postint,
            fcard_fin = :fcard_fin,
            sato_ini = :sato_ini,
            sato_postint = :sato_postint,
            sato_fin = :sato_fin,
            etco2 = :etco2,
            bis = :bis,
            despertar = :despertar,
            tiempoqx = :tiempoqx,
            induccionpropofol = :induccionpropofol,
            inducciondexmedetomidina = :inducciondexmedetomidina,
            induccionlidocaina = :induccionlidocaina,
            induccionketamina = :induccionketamina,
            induccionrnm = :induccionrnm,
            mantenimientosevorane = :mantenimientosevorane,
            mantenimientodexmedetomidina = :mantenimientodexmedetomidina,
            mantenimientolidocaina = :mantenimientolidocaina,
            mantenimientoketamina = :mantenimientoketamina,
            mantenimientosulfatomg = :mantenimientosulfatomg,
            ondasetron = :ondasetron,
            metamizol = :metamizol,
            dexametasona = :dexametasona,
            ketorol = :ketorol,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id";

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
            
            // Fármacos de Inducción (checkboxes)
            $stmt->bindValue(':induccionpropofol', (int)$data['induccionpropofol'], PDO::PARAM_INT);
            $stmt->bindValue(':inducciondexmedetomidina', (int)$data['inducciondexmedetomidina'], PDO::PARAM_INT);
            $stmt->bindValue(':induccionlidocaina', (int)$data['induccionlidocaina'], PDO::PARAM_INT);
            $stmt->bindValue(':induccionketamina', (int)$data['induccionketamina'], PDO::PARAM_INT);
            $stmt->bindValue(':induccionrnm', (int)$data['induccionrnm'], PDO::PARAM_INT);
            
            // Fármacos de Mantenimiento (checkboxes)
            $stmt->bindValue(':mantenimientosevorane', (int)$data['mantenimientosevorane'], PDO::PARAM_INT);
            $stmt->bindValue(':mantenimientodexmedetomidina', (int)$data['mantenimientodexmedetomidina'], PDO::PARAM_INT);
            $stmt->bindValue(':mantenimientolidocaina', (int)$data['mantenimientolidocaina'], PDO::PARAM_INT);
            $stmt->bindValue(':mantenimientoketamina', (int)$data['mantenimientoketamina'], PDO::PARAM_INT);
            $stmt->bindValue(':mantenimientosulfatomg', (int)$data['mantenimientosulfatomg'], PDO::PARAM_INT);
            
            // Coadyuvantes (checkboxes)
            $stmt->bindValue(':ondasetron', (int)$data['ondasetron'], PDO::PARAM_INT);
            $stmt->bindValue(':metamizol', (int)$data['metamizol'], PDO::PARAM_INT);
            $stmt->bindValue(':dexametasona', (int)$data['dexametasona'], PDO::PARAM_INT);
            $stmt->bindValue(':ketorol', (int)$data['ketorol'], PDO::PARAM_INT);

            $success = $stmt->execute();
            
            if (!$success) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL: " . print_r($errorInfo, true));
                throw new \Exception("Error al ejecutar la consulta SQL de actualización.");
            }
            
            return true;

        } catch (\PDOException $e) {
            error_log("PDO Error en updatePgsql(): " . $e->getMessage());
            throw new \Exception("Error de persistencia al actualizar datos intraoperatorios: " . $e->getMessage());
        }
    }

    
}