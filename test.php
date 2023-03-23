<?php
    $server = "localhost";
     $login = "root";
     $mdp = "";
     $db = "projetr401";
     ///Connexion au serveur MySQL
     try {
         $bdd = new PDO("mysql:host=$server;dbname=$db",$login, $mdp);
         }
     ///Capture des erreurs éventuelles
     catch (Exception $e) {
         die('Erreur : ' . $e->getMessage());
        }
        $pwd=password_hash("1234",PASSWORD_DEFAULT);
        echo $pwd;
        $res = $bdd->prepare("UPDATE utilisateur SET `mdp` = :mdp WHERE id_utilisateur = 3;");
        $res->execute(array("mdp"=>$pwd));
?>