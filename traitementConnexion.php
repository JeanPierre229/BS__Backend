<?php
session_start();
$_SESSION['error_connect'] = null;
$_SESSION['nom'] = $_SESSION['prenoms'] = $_SESSION['categories'] =  $_SESSION['mail'] = null;

$mail = $motDePasse = null;

if(!empty($_POST) && isset($_POST)){
    $mail = checkData($_POST['mail']);
    $motDePasse = sha1(checkData($_POST['motDePasse']));

    // Vérification du mail
    if(filter_var($mail, FILTER_VALIDATE_EMAIL)){
        require "connectDB.php";
        $connect = DataBase::connect();
        $requete = $connect->prepare('SELECT * FROM utilisateurs WHERE mail = :mail AND motDePasse = :motDePasse;');
        $requete->execute([
            ':mail' => $mail,
            ':motDePasse' => $motDePasse
        ]);
        if($requete->rowCount() > 0) {
            $_SESSION['nom'] = $requete->fetch()['nom'];
            $_SESSION['prenoms'] = $requete->fetch()['prenoms'];
            $_SESSION['categories'] = $requete->fetch()['categories'];
            header("Location: dashboardUser/pages/dashboard.php");
        }else{
            $_SESSION['error_connect'] = "Erreur, Identifiants incorrects !";
            header("Location: connexion.php");
        }
    }else{
        $_SESSION['error_connect'] = "Erreur, format de mail incorrect";
        header("Location: connexion.php");
    }
}
function checkData($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlentities($data);
    return $data;
}

?>