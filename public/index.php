

<?php
require_once("../negocios/NDatosPersonales.php");
header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json; charset=utf-8');
/**
 * Aqui empiezan las rutas de Cargo
 */

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['REQUEST_URI'] === '/') {        
   echo "chau";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/saveDatosPersonales') {     

        
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $formulario = new NDatosPersonales();
        $total = $formulario->saveDatosPersonales($data);
}  

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/save') {     
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $formulario = new NFormulario();
    $formulario->guardarDatos($data);
    

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Datos guardados correctamente',
        'received_data' => $data // Opcional: para debugging
    ]);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['REQUEST_URI'] === '/servicios') {        
    $formulario  = new NFormulario();
    return $formulario->getServicios();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['REQUEST_URI'] === '/servicios') {        
    $formulario  = new NFormulario();
    return $formulario->getServicios();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['REQUEST_URI'] === '/especialidades') {        
    $formulario  = new NFormulario();
    return $formulario->getEspecialidades();
}

function getParametrosGet() {
    $parametros = [];
    foreach ($_GET as $key => $value) {
        $parametros[$key] = filter_input(INPUT_GET, $key, FILTER_UNSAFE_RAW);
    }
    return $parametros;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($request_path === '/totalAyer') {

        $fecha = $_POST['fecha'] ?? null;
        $servicio = $_POST['servicio'] ?? null;

        if ($fecha === null || $servicio === null) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Missing required parameters: fecha and servicio']);
            exit;
        }
        $data = ['fecha' => $fecha, 'servicio' => $servicio];
        $formulario = new NFormulario();

        // Get the total and return a JSON response
        $total = $formulario->getTotalAyer($data);
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'data' => $total
        ]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/totalAyer2') {     
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    

    if ($data['fecha'] !== null || $data['servicio'] !== null) {
        $formulario = new NFormulario();
        $total = $formulario->getTotalAyer($data);
        
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'data' => ($total)
        ]);
        exit;
    }else{
        http_response_code(400);
        echo json_encode(['error' => $data]);
        exit;
    }
    
}  


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] === '/buscar') {     
    
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
    
        $formulario = new NFormulario();
        $total = $formulario->buscar($data);
        
} 

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['REQUEST_URI'] === '/comprobarMes') {     

    
        $formulario = new NFormulario();
        $total = $formulario->comprobarMes ("2025-08-08");
        
} 