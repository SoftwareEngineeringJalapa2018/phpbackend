<?php

    function conectar (){
        $serverName = "192.168.1.115\DEVBC";
        $connectionInfo = array("Database"=>"AdventureWorks2014","UID"=>"SA","PWD"=>"1234","CharacterSet"=>"UTF-8");

        $conn = sqlsrv_connect($serverName,$connectionInfo);
    
        if ($conn) {
            return $conn;
        } else {
            throw new Exception('Error al conectar');
        }
    }
    


    

?>