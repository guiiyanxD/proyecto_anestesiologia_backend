<?php
require_once("../datos/DDatosPostOperatorios.php");
class NDatosPostOperatorios {
    private $capaDatos;
    public function __construct(){
        $this->capaDatos = new DDatosPostOperatorios();
    }

    public function saveDatosPostOperatorios($data) {
        try{
            $data['created_at'] = date('Y-m-d H:i:s');
            $data = $this->mapAndCleanUpdateData($data);
            $this->capaDatos->insertPostOperatorios($data);

            header('Content-Type: application/json'); 
            http_response_code(200);

            echo json_encode([
                'status' => 'success',
                'message' => 'Datos post-operatorios guardados correctamente'
            ]);
            exit;

        }catch(\Exception $e){
            header('Content-Type: application/json'); 
            http_response_code(400); 
            echo json_encode([
                'status' => 'failed',
                'message' => 'Error al guardar los datos post-operatorios: ' . $e->getMessage()
            ]);
            exit;

        }    
    }

    /**
     * Mapea y limpia los datos de entrada, asegurando que los tipos sean consistentes.
     * Puedes usar esto para castear valores a FLOAT, INT, etc.
     */
    private function mapAndCleanUpdateData(array $data): array
    {
        return [
            'id'                            => (string)$data['userId'],
            'recuperacionPostAnestesia'     => (int)$data['recuperacionPostAnestesia'],
            'ramsay'                        => (int)$data['ramsay'],
            'evaIngreso'                    => (int)$data['evaIngreso'],
            'eva1hr'                        => (int)$data['eva1hr'],
            'nauseas'                       => (bool)$data['nauseas'],
            'vomitos'                       => (bool)$data['vomitos'],
            'consumoAnalgesico'             => (bool)$data['consumoAnalgesico'],
            'tipoAnalgesico'                => (string)($data['tipoAnalgesico'] ?? ""),
            'depresionRespiratoria'         => (bool)$data['depresionRespiratoria'],
            'spo2Bajo'                      => (string)($data['spo2Bajo'] ?? 0),
            'created_at'                    => $data['created_at']

        ];
    }
}


