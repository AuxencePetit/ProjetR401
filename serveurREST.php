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