


<?php
//API AIzaSyDxiJn_COshebr3jCB1l_p-SOF14ipBTRk

require('../vendor/autoload.php');
use Google\Cloud\BigQuery\BigQueryClient;


class Connection{
    private $bigQuery;
    private $dataset;
    private $table;

    public function __construct() {
        if($this->bigQuery == null){
            $this->bigQuery = new BigQueryClient([
               
                'projectId' => '',
            ]);
            $this->dataset = $this->bigQuery->dataset('second_proy_at');
            $this->table = $this->dataset->table('datos_personales');
        }
    }

    public function getBigQuery() {
        return $this->bigQuery;
    }

    public function getDataset() {
        return $this->dataset;
    }

    public function getTable() {
        return $this->table;
    }

    public function setTable($tableName) {
        $this->table = $this->dataset->table($tableName);
    }
}
?>
