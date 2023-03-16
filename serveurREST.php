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

    /// Identification du type de méthode HTTP envoyée par le client
    $http_method = $_SERVER['REQUEST_METHOD'];
    include("jwt_utils.php");
    $bearer_token = get_bearer_token();
    switch ($http_method){
        /// Cas de la méthode GET
        case "GET" :
            /// Récupération des critères de recherche envoyés par le Client
            if(is_jwt_valid($bearer_token)){
               if (isset($_GET['id'])){
                    $req = $linkpdo->prepare("SELECT * FROM `article` WHERE id_article = ?;");
                    $req->execute(array($_GET['id']));
                    $matchingData = $req->fetchAll();
                }else{
                    $req = $linkpdo->prepare("SELECT * FROM `article`");
                    $req->execute();
                    $matchingData = $req->fetchAll();
                }
                /// Envoi de la réponse au Client
                deliver_response(200, "Votre message", $matchingData);
                break; 
            }
        /// Cas de la méthode POST
        case "POST" :
            /// Récupération des données envoyées par le Client
            $postedData = file_get_contents('php://input');
            $data = json_decode($postedData);
            $contenue = $data->contenue;
            $auteur = $data->auteur;
            if (!empty($data->phrase)){
                $req = $linkpdo->prepare("INSERT INTO article (contenue, id_utilisateur) VALUES (:contenue, :auteur)");
                $req->execute(array('contenue' => $contenue, 'auteur'=>$auteur));
            }
            /// Traitement
            /// Envoi de la réponse au Client
            deliver_response(200, "Votre message", NULL);
            break;
        /// Cas de la méthode PUT
        case "PUT" :  
            /// Traitement
            /// Envoi de la réponse au Client
            $postedData = file_get_contents('php://input');
            $postedData = json_decode($postedData);
            if (!empty($postedData->phrase)){
                $req = $linkpdo->prepare("UPDATE `article` SET `contenue` = :phrase WHERE `article`.`id_article` = :id;");
                $req->execute(array('contenue' => $postedData->phrase,
                                    'id' => $postedData->id));
            }
            deliver_response(201, "Votre message", NULL);
            break;
            /// Récupération des données envoyées par le Client
            
        /// Cas de la méthode DELETE
        case "DELETE" :
            /// Récupération des données envoyées par le Client
            if (isset($_GET['id'])){
                $req = $linkpdo->prepare("DELETE FROM article WHERE `article`.`id_article` = ?");
                $req->execute(array($_GET['id']));
            }
            /// Traitement
            /// Envoi de la réponse au Client
            deliver_response(200, "Votre message", NULL);
            break;
        /// Cas de la méthode LIKE
        case "LIKE" :
            /// Récupération des données envoyées par le Client
            $postedData = file_get_contents('php://input');
            $data = json_decode($postedData);
            $idUtilisateur = $data->idUtilisateur;
            $idArticle = $data->idArticle;
            if (!empty($data->idUtilisateur)){
                if(!empty($data->idArticle)){
                    ///verification de vote deja existant
                    $req = $linkpdo->prepare("SELECT * FROM `reagir` WHERE id_article = :id_article AND id_utilisateur = :id_utilisateur;");
                    $req->execute(array('id_article'=> $idArticle, 'id_utilisateur'=> $idUtilisateur));
                    if($req = NULL){
                        $req = $linkpdo->prepare("INSERT INTO reagir(id_utilisateur, id_article, liker, disliker) VALUES (:id_utilisateur, :id_article, :liker, :disliker)");
                        $req->execute(array('id_utilisateur' => $idUtilisateur, 'id_article' => $idArticle, 'liker'=>1, 'disliker'=>0));
                    }else{
                        $req = $linkpdo->prepare("UPDATE `reagir` SET `liker` = :likefalse , 'disliker'= :disliketrue WHERE `reagir`.`id_article` = :id_article AND 'reagir'.'id_utilisateur'= :id_utilisateur;");
                        $req->execute(array('id_article'=> $idArticle, 'id_utilisateur'=>$idUtilisateur,'likefalse'=>1, 'disliketrue'=>0));
                    }
                }   
            }
            /// Envoi de la réponse au Client
            deliver_response(200, "Votre message", NULL);
            break;
        case "DISLIKE" :
            /// Récupération des données envoyées par le Client
            $postedData = file_get_contents('php://input');
            $data = json_decode($postedData);
            $idUtilisateur = $data->idUtilisateur;
            $idArticle = $data->idArticle;
            if(!empty($data->idUtilisateur)){
                if(!empty($data->idArticle)){
                    ///verification de vote deja existant
                    $req = $linkpdo->prepare("SELECT * FROM `reagir` WHERE id_article = :id_article AND id_utilisateur = :id_utilisateur;");
                    $req->execute(array('id_article'=> $idArticle, 'id_utilisateur'=> $idUtilisateur));
                    if($req = NULL){
                        $req = $linkpdo->prepare("INSERT INTO reagir(id_utilisateur, id_article, liker, disliker) VALUES (:id_utilisateur, :id_article, :liker, :disliker)");
                        $req->execute(array('id_utilisateur' => $idUtilisateur, 'id_article' => $idArticle, 'liker'=>0, 'disliker'=>1));
                    }else{
                        $req = $linkpdo->prepare("UPDATE `reagir` SET `liker` = :likefalse , 'disliker'= :disliketrue WHERE `reagir`.`id_article` = :id_article AND 'reagir'.'id_utilisateur'= :id_utilisateur;");
                        $req->execute(array('id_article'=> $idArticle, 'id_utilisateur'=>$idUtilisateur,'likefalse'=>0, 'disliketrue'=>1));
                    }   
                }
            }
            ///Envoi de la réponse au Client
            deliver_response(200,"message", NULL);
            break;
        //cas d'erreur
        default :
            /// Récupération de l'identifiant de la ressource envoyé par le Client
            if (!empty($_GET['mon_id'])){
            /// Traitement
            }
            /// Envoi de la réponse au Client
            deliver_response(200, "Votre message", NULL);
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