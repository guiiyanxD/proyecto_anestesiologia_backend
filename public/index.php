<?php
use Dotenv\Dotenv;

define('ROOT_PATH', dirname(__DIR__));

require_once("../negocios/NDatosPersonales.php");
require_once("../negocios/NDatosIntraOperatorios.php");
require_once("../negocios/NDatosPostOperatorios.php");
require_once("../negocios/NPerfil.php");
require_once("../vendor/autoload.php");

// ========================================
// CONFIGURACIÓN CORS
// ========================================
header("Access-Control-Allow-Origin: https://proyecto-anestesiologia-front-463127106629.us-central1.run.app");
header("Access-Control-Allow-Origin: https://proyecto-anestesiologia-frontend-107701930483.europe-west1.run.app");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");

// Maneja peticiones preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// ========================================
// CARGAR VARIABLES DE ENTORNO
// ========================================
try {
    $dotenv = Dotenv::createImmutable(ROOT_PATH); 
    $dotenv->safeLoad();
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al cargar configuración: ' . $e->getMessage()
    ]);
    exit;
}

// ========================================
// RUTAS
// ========================================

/**
 * Raiz - Para verificar que el servicio está funcionando
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['REQUEST_URI'] === '/') {
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'API Anestesiología funcionando correctamente',
        'version' => '1.0.0'
    ]);
    exit;
}

/**
 * Get User Data By ID
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/user') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $perfil = new NPerfil();
    $result = $perfil->getUserData($data);
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
    exit;
}

/**
 * Get últimos 10 registros
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['REQUEST_URI'] === '/getUltimosRegistros') {
    $perfil = new NPerfil();
    $result = $perfil->getUltimos10();
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
    exit;
}

/**
 * Get todos los registros
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['REQUEST_URI'] === '/ver-todo') {
    $perfil = new NPerfil();
    $result = $perfil->getTodosRegistros();
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
    exit;
}

/**
 * Insert Data To Datos_Personales table
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/saveDatosPersonales') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $formulario = new NDatosPersonales();
    $result = $formulario->saveDatosPersonales($data);
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
    exit;
}


/**
 * Update Datos Personales
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/updateDatosPersonales') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Validar que venga el ID
    if (!isset($data['id']) || empty($data['id'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'ID del paciente es requerido'
        ]);
        exit;
    }
    
    try {
        $formulario = new NDatosPersonales();
        $result = $formulario->updateDatosPersonales($data);
        
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Datos actualizados correctamente',
            'data' => $result
        ]);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}



/**
 * Save Datos Intra Operatorios
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/saveDatosIntraOperatorios') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $formulario = new NDatosInstraOperatorios();
    $result = $formulario->saveDatosIntraOperatorios($data);
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
    exit;
}

/**
 * Save Datos Post Operatorios
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/saveDatosPostOperatorios') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $formulario = new NDatosPostOperatorios();
    $result = $formulario->saveDatosPostOperatorios($data);
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
    exit;
}

// Ruta no encontrada
http_response_code(404);
echo json_encode([
    'status' => 'error',
    'message' => 'Endpoint not found'
]);
exit;