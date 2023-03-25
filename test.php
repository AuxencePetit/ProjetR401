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
        $list = array();
        $req = $linkpdo->prepare("SELECT * FROM `reagir` WHERE id_article = :id_article AND id_utilisateur = :id_utilisateur;");
        $req->execute(array('id_article'=> 3, 'id_utilisateur'=> 2));
        $data = $req->fetchAll();
        if($data == null){
            echo "null";
        }else{
            var_dump($data);
        }
    /*
    
        $pwd=password_hash("1234",PASSWORD_DEFAULT);
        echo $pwd;
        $res = $linkpdo->prepare("UPDATE utilisateur SET `mdp` = :mdp WHERE id_utilisateur = 3;");
        $res->execute(array("mdp"=>$pwd));
        */
?>