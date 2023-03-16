<?php
    $server = "localhost";
    $login = "root";
    $mdp = "";
    $db = "projetr401";
    $data = (array) json_decode(file_get_contents('php://input'), TRUE);
    ///Connexion au serveur MySQL
    try {
        $bdd = new PDO("mysql:host=$server;dbname=$db",$login, $mdp);
        }
    ///Capture des erreurs éventuelles
    catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
       }
   $res = $bdd->prepare("Select Id_utilisateur,login,role ,mdp FROM utilisateur WHERE login = :login");
   $res->execute(array("login"=>$data['username']));
       foreach($res as $row){
           $id= $row['Id_utilisateur'];
           $loginFromBDD = $row['login'];
           $passwordHashedFromBDD = $row['mdp'];
           $role = $row['role'];
       }
   if($data['username'] == $loginFromBDD AND password_verify($data["password"], $passwordHashedFromBDD)) {
       include("jwt_utils.php");
       $header = array("alg"=>"HS256","typ"=>"JWT");
       $payload = array("user_id"=>$id , "role"=>$role , 'exp'=>(time()+700));
       $tokenJWT = generate_jwt($header,$payload);
       echo $tokenJWT;
   } else {
       echo "identifiant incorrect";
   } 
?>