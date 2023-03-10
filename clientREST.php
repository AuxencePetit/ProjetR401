<?php
    ////////////////// Cas des méthodes GET et DELETE //////////////////
    
   
    ////////////////// Cas des méthodes POST et PUT //////////////////
    /// Déclaration des données à envoyer au Serveur
    if(isset($_GET["IdModif"])){
        $data = array("id" => $_GET["IdModif"], "phrase" => $_GET["NewPhrase"]);
        $data_string = json_encode($data);
        /// Envoi de la requête
        $result = file_get_contents(
                                        'http://localhost/tpR401/tp2/serveurREST.php',
                                        false,stream_context_create(array(
                                        'http' => array('method' => 'PUT', // ou PUT
                                        'content' => $data_string,
                                        'header' => array('Content-Type: application/json'."\r\n"
                                        .'Content-Length: '.strlen($data_string)."\r\n"))))
        );
        /// Dans tous les cas, affichage des résultats
        echo '<pre>' . htmlspecialchars($result) . '</pre>';
    }
    if(isset($_GET['sppr'])){
        $result = file_get_contents(
            'http://localhost/tpR401/tp2/serveurREST.php?id='.$_GET['sppr'],
            false,
            stream_context_create(array('http' => array('method' => 'DELETE'))) // ou DELETE
    );
    }
    if(isset($_GET['LAphrase'])){
        $data = array("phrase" => $_GET['LAphrase']);
        $data_string = json_encode($data);
        /// Envoi de la requête
        $result = file_get_contents(
                                        'http://localhost/tpR401/tp2/serveurREST.php',
                                        false,stream_context_create(array(
                                        'http' => array('method' => 'POST', // ou PUT
                                        'content' => $data_string,
                                        'header' => array('Content-Type: application/json'."\r\n"
                                        .'Content-Length: '.strlen($data_string)."\r\n"))))
        );
        echo '<pre>' . htmlspecialchars($result) . '</pre>';
    }
    $result = file_get_contents(
                                'http://localhost/tpR401/tp2/serveurREST.php',
                                false,
                                stream_context_create(array('http' => array('method' => 'GET'))) // ou DELETE
    );
    $result = json_decode($result, true);
    $data = $result['data'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="clientREST.php" method="GET">
        <label for="LAphrase">AJOUTER VOTRE PHRASE</label>
        <input type="text" name="LAphrase">
        <input type="submit" value="Ajouter">
    </form>
    <table>
                <tr>
                    <th>id</th>
                    <th>Phrase</th>
                    <th>Manip</th>
                </tr>
                <?PHP
                    foreach ($data as $valeur) {
                        echo "<tr><td>".$valeur[0]."</td>"."<td>".$valeur[1]."<td><a href='clientREST.php?sppr=".$valeur[0]."'>SUPPRIMER</a> <a href='modif.php?id=".$valeur[0]."'>MODIF</a></td></tr>";

                    }
                ?>
    </table>
    <a href="clientREST?id="></a>
    <style>
        table{
            width: fit-content;
            background-color: blue;
            border-radius: 20px;
            padding: 5px;
            color: white;
        }
        td{
            margin: 3px;
            font-size: 1.5em;
            font-weight: bolder;
            color: blue;
            background-color: white;
            border-radius: 20px;
            padding: 5px;
        }
    </style>
</body>
</html>
