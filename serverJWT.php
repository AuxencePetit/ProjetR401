<?php
    include("jwt_utils.php");
    $header = array("alg"=>"HS256","typ"=>"JWT");
    $payload = array("user_id"=>"1234", 'exp'=>(time()+700));
    $user = "aux";
    $mdp = "1234";
    if(isset($_POST['mdp']) AND isset($_POST['user'])){
        if($_POST['mdp']===$mdp AND $user === $_POST['user']){
        $tokenJWT = generate_jwt($header,$payload);
        echo $tokenJWT;
        }else{
            echo "identifiant incorrect";
        }
    }
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
    <form action="serverJWT.php" method="POST">
        <input type="text" name="user">
        <input type="password" name="mdp">
        <input type="submit">
    </form>
</body>
</html>