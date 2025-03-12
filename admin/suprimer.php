<?php
require_once("connexion/connexion.php");
// Vérifier si l'ID est passé en paramètre
if (isset($_GET['sup'])) {
    $id = intval($_GET['sup']);

    // Préparer la requête de suppression
    $sql = "DELETE FROM `user` WHERE matricule = ?";
    $stmt = $pdo->prepare($sql);

    // Exécuter la requête
    if ($stmt->execute([$id])) {
        $sms="supprimé avec succès.";
    } else {
        $sms= "Erreur lors de la suppression ";
    }
    header("location:admin.php?sms=$sms");
    $stmt->closeCursor();
    exit();
} else {
    echo "Aucun ID fourni pour la suppression.";
}


?>