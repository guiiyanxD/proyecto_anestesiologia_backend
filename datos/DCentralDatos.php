<?php   

class DCentralDatos {
    private $connection;
    public function __construct() {
        $this->connection = new DDatosPersonales();
    }
}