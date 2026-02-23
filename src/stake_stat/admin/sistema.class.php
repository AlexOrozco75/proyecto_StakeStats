<?php
require_once(__dir__."/config.php");

class sistema{
    
    var $db;

    function conectar(){
       $this->db = new PDO(dbdriver.":host=".dbhost.";dbname=".dbname,dbuser, dbpassword);

    }
    function alerta($tipo,$mensaje){

        if(!is_null($tipo)&&!is_null($mensaje)){
        $alerta = array();
        $alerta['tipo'] = $tipo;
        $alerta['mensaje'] = $mensaje;
        include(__DIR__."/views/estados/alerta.php");
        }
        
    }
};

?>

