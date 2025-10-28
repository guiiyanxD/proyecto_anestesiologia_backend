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

    public function obtenerDatos($id) {
        return $this->capaDatos->obtenerDatos($id);
    }

    public function actualizarDatos($id, $data) {
        return $this->capaDatos->actualizarDatos($id, $data);
    }
}