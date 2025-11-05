<?php

class PgsqlConnection {

    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];

        $dsn = "pgsql:host=$host;dbname=$dbname";
        
        try {
            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            error_log("Error de conexión a la BD: " . $e->getMessage());
            throw new Exception("No se pudo conectar a la base de datos.");
        }
    }
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new PgsqlConnection();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}

