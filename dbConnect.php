<?php
    $server = "localhost";
     $login = "root";
     $mdp = "";
     $db = "projetr401";
     ///Connexion au serveur MySQL
     try {
         $linkpdo = new PDO("mysql:host=$server;dbname=$db",$login, $mdp);
         }
     ///Capture des erreurs éventuelles
     catch (Exception $e) {
         die('Erreur : ' . $e->getMessage());
        }
?>