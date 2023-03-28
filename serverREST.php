<?php

    /// Librairies éventuelles (pour la connexion à la BDD, etc.)
    /// include('mylib.php');
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
    /// Paramétrage de l'entête HTTP (pour la réponse au Client)
    header("Content-Type:application/json");
    $http_method = $_SERVER['REQUEST_METHOD'];
    include("jwt_utils.php");
    $bearer_token = get_bearer_token();
    $payload = getPayload($bearer_token);

    function getNbReaction($type,$id){
        include("dbConnect.php");
        if($type === "liker" || $type ==="disliker"){
            $req = $linkpdo->prepare("SELECT count(reagir.".$type.") as num FROM reagir WHERE reagir.".$type." = 1 AND reagir.Id_article = ?;");
            $req->execute(array($id));
            $matchingData = $req->fetchAll();
            $nb = $matchingData[0]["num"];
            return $nb;
        }
       
    }
    function getListReact($type,$id){
        include("dbConnect.php");
        $list = array();
        if($type === "liker" || $type ==="disliker"){
            $req = $linkpdo->prepare("SELECT utilisateur.login FROM reagir,utilisateur WHERE reagir.".$type." = 1 AND utilisateur.Id_utilisateur = reagir.Id_utilisateur AND reagir.Id_article = ?;");
            $req->execute(array($id));
            $matchingData = $req->fetchAll();
            foreach($matchingData as $user){
                array_push($list,$user[0]);
            }
            return $list;
        }
        
    }
    function rechercheArticle($id){
        $bearer_token = get_bearer_token();
        $payload = getPayload($bearer_token);
        include("dbConnect.php");
        if($id == null){ // $qui est vide, on retourne faux
            $req = $linkpdo->prepare("SELECT * FROM `article`");
            $req->execute();
            $matchingData = $req->fetchAll(PDO::FETCH_ASSOC);
        }else{$req = $linkpdo->prepare("SELECT * FROM `article` WHERE id_article = ?;");
            $req->execute(array($id));
            $matchingData = $req->fetchAll(PDO::FETCH_ASSOC);
        }
        $allMessages = array();
        foreach($matchingData as $article){
            $a = array();
            if(is_jwt_valid($bearer_token)){
                    $a["nbLike"] = getNbReaction("liker",$article["Id_article"]);
                    $a["nbDislike"] = getNbReaction("disliker",$article["Id_article"]);
                    if($payload->role ==="moderateur"){
                        $a["ListLikeur"] = getListReact("liker",$article["Id_article"]);
                        $a["ListDislikeur"] = getListReact("disliker",$article["Id_article"]);
                    }
                }
            $a["idArticle"] = $article["Id_article"];
            $a["text"] = $article["contenue"];
            $a["date"] = $article["dateDePublication"];
            $a["IdUser"] = $article["Id_utilisateur"];
            array_push($allMessages,$a);
        }
        return $allMessages;
    }
    ///verification de vote deja existant
    function asReacted($idArticle){
        $bearer_token = get_bearer_token();
        $payload = getPayload($bearer_token);
        include("dbConnect.php");
        $req = $linkpdo->prepare("SELECT * FROM `reagir` WHERE id_article = :id_article AND id_utilisateur = :id_utilisateur");
        $req->execute(array('id_article'=> $idArticle, 'id_utilisateur'=>$payload->user_id));
        $data = $req->fetchAll();
        if($data == null){
           return false;
        }else{
            return true;
        }
    }
    /// Identification du type de méthode HTTP envoyée par le client
    switch ($http_method){
        /// Cas de la méthode GET
        case "GET" :
                /// Récupération des critères de recherche envoyés par le Client 
               if (isset($_GET['id'])){
                    $matchingData = rechercheArticle($_GET['id']);
                }elseif(isset($_GET['MyMessage'])){
                    if($_GET['MyMessage'] == 1){
                        if($payload->role ==="publisher"){
                            $req = $linkpdo->prepare("SELECT * FROM `article` WHERE Id_utilisateur = ?");
                            $req->execute(array($payload->user_id));
                            $matchingData = $req->fetchAll(PDO::FETCH_ASSOC);
                            $allMessages = array();
                            foreach($matchingData as $article){
                                $a = array();
                                if(is_jwt_valid($bearer_token)){
                                        $a["nbLike"] = getNbReaction("liker",$article["Id_article"]);
                                        $a["nbDislike"] = getNbReaction("disliker",$article["Id_article"]);
                                    }
                                $a["idArticle"] = $article["Id_article"];
                                $a["text"] = $article["contenue"];
                                $a["date"] = $article["dateDePublication"];
                                $a["IdUser"] = $article["Id_utilisateur"];
                                array_push($allMessages,$a);
                            }
                            $matchingData = $allMessages;
                        }else {
                            deliver_response(401,"Unauthorized",NULL);
                        }
                    }
                }else{
                    $matchingData = rechercheArticle(null);
                }
                /// Envoi de la réponse au Client
                if($matchingData == null){
                    deliver_response(404, "Not Found", NULL);
                }else{
                    deliver_response(200, "Chargement des messages réalisé avec succes", $matchingData);
                }
                break;
        /// Cas de la méthode POST
        case "POST" :
            ///verification de la validité du token
            if(is_jwt_valid($bearer_token)){
                if($payload->role ==="publisher"){
                    /// Récupération des données envoyées par le Client
                    $postedData = file_get_contents('php://input');
                    $data = json_decode($postedData);
                    $idUtilisateur = $payload->user_id; 
                    $type =$data->action;
                    switch($data->action){
                        /// Cas de la méthode LIKE
                        case "liker" :
                                if(!empty($data->idArticle)){
                                        $idArticle = $data->idArticle;
                                         ///verification de vote deja existant
                                        if(asReacted($idArticle) == false){
                                            $req = $linkpdo->prepare("INSERT INTO reagir(id_utilisateur, id_article, liker, disliker) VALUES (?, ?, 1, null)");
                                            $rs=$req->execute(array($idUtilisateur,$idArticle));
                                        }else{
                                            $req = $linkpdo->prepare("UPDATE `reagir` SET `liker` = 1 , disliker= null WHERE `reagir`.`id_article` = :id_article AND reagir.id_utilisateur= :id_utilisateur;");
                                            $rs=$req->execute(array('id_article'=> $idArticle, 'id_utilisateur'=>$idUtilisateur));
                                        }
                                        if($rs == null){
                                            deliver_response(404, "Not Found", NULL);
                                        }else{
                                                deliver_response(200, "like ajouté avec succes",NULL);
                                        }
                                        break;
                                }   
                                /// Envoi de la réponse au Client
                                deliver_response(200, "OK", $payload->user_id);
                            break;
                        /// Cas de la méthode DISLIKE
                        case "disliker" :
                                    if(!empty($data->idArticle)){
                                        $idArticle = $data->idArticle;
                                        ///verification de vote deja existant
                                        if(asReacted($idArticle) == false){
                                            $req = $linkpdo->prepare("INSERT INTO reagir(id_utilisateur, id_article, liker, disliker) VALUES (:id_utilisateur, :id_article, null, 1)");
                                            $rs=$req->execute(array('id_utilisateur' => $idUtilisateur, 'id_article' => $idArticle));
                                        }else{
                                            $req = $linkpdo->prepare("UPDATE `reagir` SET `liker` = null , disliker = 1 WHERE `reagir`.`id_article` = :id_article AND reagir.id_utilisateur= :id_utilisateur;");
                                            $rs=$req->execute(array('id_article'=> $idArticle, 'id_utilisateur'=>$idUtilisateur));
                                        }
                                        if($rs == null){
                                                deliver_response(404, "Not Found", NULL);
                                        }else{
                                                deliver_response(200, "dislike ajouté avec succes",NULL);
                                        }
                                        break;
                                    }
                                ///Envoi de la réponse au Client
                            break;
                        /// Cas de la méthode poster
                        case "poster" :
                                    if (!empty($data->contenu)){
                                        $req = $linkpdo->prepare("INSERT INTO article (contenue, id_utilisateur) VALUES (:contenue, :auteur)");
                                        $rs = $req->execute(array('contenue' => $data->contenu, 'auteur'=>$idUtilisateur));
                                    }
                                    /// Envoi de la réponse au Client
                                    if($rs == null){
                                        deliver_response(424, "	Method failure", NULL);
                                    }else{
                                            deliver_response(200, "dislike ajouté avec succes",NULL);
                                    }
                                    break;
                    } 
                }else{
                    deliver_response(401,"Unauthorized",NULL);
                    break;
                }                 
            }else{
                deliver_response(401,"Unauthorized",NULL);
            }
            break;   
        /// Cas de la méthode PUT
        case "PUT" :  
            ///verification de la validité du token
            if(is_jwt_valid($bearer_token)){
                if($payload->role ==="publisher"){
                    $postedData = file_get_contents('php://input');
                    $data = json_decode($postedData);
                    $idUtilisateur = $payload->user_id;
                    if (!empty( $data->contenu)){
                        $req = $linkpdo->prepare("UPDATE `article` SET `contenue` = ? WHERE `article`.`id_article` = ? AND article.Id_utilisateur = ? ;");
                        $rs = $req->execute(array($data->contenu,$data->id,$idUtilisateur));
                    }
                }
                /// Envoi de la réponse au Client
                if($rs == null){
                    deliver_response(404, "Not Found", NULL);
                }else{
                    deliver_response(201, "Modified", NULL);
                }
                break;
            }else{
                deliver_response(401,"Unauthorized",NULL);
            }
            break;
        /// Cas de la méthode DELETE
        case "DELETE" :
            ///verification de la validité du token
            if(is_jwt_valid($bearer_token)){
                if( $payload->role === "moderateur" ){
                    if (isset($_GET['id'])){
                        $req = $linkpdo->prepare("DELETE FROM article WHERE `article`.`id_article` = ?");
                        $req->execute(array($_GET['id']));
                    }
                    /// Traitement
                    /// Envoi de la réponse au Client
                    if($req == null){
                        deliver_response(404, "Not Found", NULL);
                    }else{
                        deliver_response(200, "Suppression réalisé avec succes", NULL);
                    }
                    
                    break;
                }
                else if( $payload->role ==="publisher"){
                    if (isset($_GET['id'])){
                        $req = $linkpdo->prepare("DELETE FROM article WHERE `article`.`id_article` = ? AND article.Id_utilisateur = ?");
                        $req->execute(array($_GET['id'],$payload->id));
                    }
                    if($req == null){
                        deliver_response(200, "Suppression réalisé avec succes", NULL);
                    }else{
                        deliver_response(404, "Not Found", NULL);
                    }
                    break;
                }
            }else{
                deliver_response(401,"Unauthorized",NULL);
            }
            break;
        //cas d'erreur
        default :
            /// Récupération de l'identifiant de la ressource envoyé par le Client
            if (!empty($_GET['mon_id'])){
            /// Traitement
            }
            /// Envoi de la réponse au Client
            deliver_response(400, "Bad Request", NULL);
            break;
    }
    /// Envoi de la réponse au Client
    function deliver_response($status, $status_message, $data){
        /// Paramétrage de l'entête HTTP, suite
        header("HTTP/1.1 $status $status_message");
        /// Paramétrage de la réponse retournée
        $response['status'] = $status;
        $response['status_message'] = $status_message;
        $response['data'] = $data;
        /// Mapping de la réponse au format JSON
        $json_response = json_encode($response);
        echo $json_response;
    }

?>