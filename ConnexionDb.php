<?php

 $UserName = 'root';
 $Passeword = 'maxime';
 $HostName ='localhost';
 $DataBase = 'archiva';

 function connexionDb($a,$b,$c,$d) {
    $conn=mysqli_connect($a,$b,$c,$d);
    if(!$conn){
        die("Couldn't connect");
        //  "veuilez reessayer";
    }
    echo "connection réussie";
    return $conn;
 } 