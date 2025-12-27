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
            $stmt->bindValue(':induccionpropofol', $data['induccionpropofol'], PDO::PARAM_BOOL);
            $stmt->bindValue(':inducciondexmedetomidina', $data['inducciondexmedetomidina'], PDO::PARAM_BOOL);
            $stmt->bindValue(':induccionlidocaina', $data['induccionlidocaina'], PDO::PARAM_BOOL);
            $stmt->bindValue(':induccionketamina', $data['induccionketamina'], PDO::PARAM_BOOL);
            $stmt->bindValue(':induccionrnm', $data['induccionrnm'], PDO::PARAM_BOOL);
            
            // Fármacos de Mantenimiento (checkboxes)
            $stmt->bindValue(':mantenimientosevorane', $data['mantenimientosevorane'], PDO::PARAM_BOOL);
            $stmt->bindValue(':mantenimientodexmedetomidina', $data['mantenimientodexmedetomidina'], PDO::PARAM_BOOL);
            $stmt->bindValue(':mantenimientolidocaina', $data['mantenimientolidocaina'], PDO::PARAM_BOOL);
            $stmt->bindValue(':mantenimientoketamina', $data['mantenimientoketamina'], PDO::PARAM_BOOL);
            $stmt->bindValue(':mantenimientosulfatomg', $data['mantenimientosulfatomg'], PDO::PARAM_BOOL);
            
            // Coadyuvantes (checkboxes)
            $stmt->bindValue(':ondasetron', $data['ondasetron'], PDO::PARAM_BOOL);
            $stmt->bindValue(':metamizol', $data['metamizol'], PDO::PARAM_BOOL);
            $stmt->bindValue(':dexametasona', $data['dexametasona'], PDO::PARAM_BOOL);
            $stmt->bindValue(':ketorol', $data['ketorol'], PDO::PARAM_BOOL);
            
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
            $stmt->bindValue(':induccionpropofol', $data['induccionpropofol'], PDO::PARAM_BOOL);
            $stmt->bindValue(':inducciondexmedetomidina', $data['inducciondexmedetomidina'], PDO::PARAM_BOOL);
            $stmt->bindValue(':induccionlidocaina', $data['induccionlidocaina'], PDO::PARAM_BOOL);
            $stmt->bindValue(':induccionketamina', $data['induccionketamina'], PDO::PARAM_BOOL);
            $stmt->bindValue(':induccionrnm', $data['induccionrnm'], PDO::PARAM_BOOL);
            
            // Fármacos de Mantenimiento (checkboxes)
            $stmt->bindValue(':mantenimientosevorane', $data['mantenimientosevorane'], PDO::PARAM_BOOL);
            $stmt->bindValue(':mantenimientodexmedetomidina', $data['mantenimientodexmedetomidina'], PDO::PARAM_BOOL);
            $stmt->bindValue(':mantenimientolidocaina', $data['mantenimientolidocaina'], PDO::PARAM_BOOL);
            $stmt->bindValue(':mantenimientoketamina', $data['mantenimientoketamina'], PDO::PARAM_BOOL);
            $stmt->bindValue(':mantenimientosulfatomg', $data['mantenimientosulfatomg'], PDO::PARAM_BOOL);
            
            // Coadyuvantes (checkboxes)
            $stmt->bindValue(':ondasetron', $data['ondasetron'], PDO::PARAM_BOOL);
            $stmt->bindValue(':metamizol', $data['metamizol'], PDO::PARAM_BOOL);
            $stmt->bindValue(':dexametasona', $data['dexametasona'], PDO::PARAM_BOOL);
            $stmt->bindValue(':ketorol', $data['ketorol'], PDO::PARAM_BOOL);

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