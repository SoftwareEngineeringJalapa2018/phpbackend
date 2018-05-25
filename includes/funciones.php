<?php

    function ejecutar($conectar,$consulta){
        $return = sqlsrv_query($conectar,$consulta);
        return $return;
    }

    function consulta_array($resultado_query,$debug=0){
        if(!$resultado_query){
            return false;
        }
        else{
            $return = array();
            while( $registro = sqlsrv_fetch_array( $resultado_query, SQLSRV_FETCH_ASSOC )) {
                array_push($return,array_change_key_case($registro));
            }
        }

        if($debug)
            var_dump($return);
        else
            return  $return;
    }
?>