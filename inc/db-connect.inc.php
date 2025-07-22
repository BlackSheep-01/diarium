<?php

/*
//for local hosting using xampp
try{
    $pdo= new PDO("mysql:host=localhost;port=3307;dbname=diary_project", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}
catch(PDOException $e){
    var_dump($e-> getMessage());
    die();
}
*/


//for server hosting using byethost. attributes are taken from sql section via vista panel
try {
    $pdo = new PDO("mysql:host=sql313.byethost16.com;dbname=b16_39530590_phpdiary", "b16_39530590", "nowifesinglelife20", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}



