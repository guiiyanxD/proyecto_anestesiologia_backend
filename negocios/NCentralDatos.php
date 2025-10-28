<?php
require_once("../datos/DCentralDatos.php");
class NCentralDatos {
    private $capaDatos;
    public function __construct() {
        $this->capaDatos = new DDatosPersonales();
    }

    public function guardarDatos($data) {
        return $this->capaDatos->guardarDatos($data);
    }
}