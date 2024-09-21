<?php 

session_start();

try {
    $db = new PDO('mysql:host=localhost;dbname=Website_Productivity;charset=utf8', 'root', 'root', [

        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,

    ]);
}
catch(Exception $e) {
    die('Error : '.$e->getMessage());
}
