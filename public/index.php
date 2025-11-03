<?php
use Dotenv\Dotenv;
define('ROOT_PATH', dirname(__DIR__));
require_once("../negocios/NDatosPersonales.php");
require_once("../negocios/NDatosIntraOperatorios.php");
require_once("../negocios/NDatosPostOperatorios.php");
require_once("../negocios/NPerfil.php");
require_once("../vendor/autoload.php");

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json; charset=utf-8');


try {
    $dotenv = Dotenv::createImmutable(ROOT_PATH); 
    $dotenv->safeLoad();

} catch (\Exception $e) {
    die("Error al cargar el archivo .env: " . $e->getMessage());
}


/**
 * Raiz
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['REQUEST_URI'] === '/') {        
   print_r($_ENV);
}

/**
 * Get User Data By ID
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/user') {     
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $perfil = new NPerfil();
    $total = $perfil->getUserData($data);
} 

/**
 * Insert Data To Datos_Personales table on BigQuery
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/saveDatosPersonales') {     
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $formulario = new NDatosPersonales();
    $total = $formulario->saveDatosPersonales($data);
} 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/saveDatosIntraOperatorios') {     
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $formulario = new NDatosInstraOperatorios();
    $total = $formulario->saveDatosIntraOperatorios($data);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/saveDatosPostOperatorios') {     
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $formulario = new NDatosPostOperatorios();
    $total = $formulario->saveDatosPostOperatorios($data);
}