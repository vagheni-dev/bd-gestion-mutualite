<?php
// Include database connection file
require_once("connexion/connexion.php");

if(isset($_POST['submit'])) {
    $matricule =htmlspecialchars($_POST['matricule']);
    $nom = htmlspecialchars($_POST['nom']);
    $montant = htmlspecialchars( $_POST['montant']);

    // Insert cotisation into database
    $sql = "INSERT INTO cotisations (matricule,nom,montant) VALUES (?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $result=$stmt->execute([$matricule,$nom,$montant]);
    if($result==1) {
        $sms="enregistrement";
    } 
    else {
        $sms="echec";
    }
    header("location:cotisations.php?sms=$sms");
}
?>

