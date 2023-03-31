<?php
    include("jwt_utils.php");
    $data = (array) json_decode(file_get_contents('php://input'), TRUE);
    ///Connexion au serveur MySQL
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        require("config.php");
        $res = $linkpdo->prepare("Select Id_utilisateur,login,role ,mdp FROM utilisateur WHERE login = :login");
        $res->execute(array("login"=>$data['username']));
            foreach($res as $row){
                $id= $row['Id_utilisateur'];
                $loginFromBDD = $row['login'];
                $passwordHashedFromBDD = $row['mdp'];
                $role = $row['role'];
            }
        if($data['username'] == $loginFromBDD AND password_verify($data["password"], $passwordHashedFromBDD)) {
            
            $header = array("alg"=>"HS256","typ"=>"JWT");
            $payload = array("user_id"=>$id , "role"=>$role , 'exp'=>(time()+7000));
            $tokenJWT = generate_jwt($header,$payload);
            echo $tokenJWT;
        } else {
            deliver_response(404,"NOT FOUND",NULL);
        } 
    }else{
        deliver_response(501,"Not Implemented",NULL);
    }
    
?>