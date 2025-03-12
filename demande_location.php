<?php
session_start();
require_once("connexion/connexion.php");

// Vérifier si $pdo est défini
if (!isset($pdo)) {
    die("Erreur de connexion à la base de données.");
}

// Gérer les demandes de location
if (isset($_POST['demande_location'])) {
    $bien_id = htmlspecialchars($_POST['bien_id']);
    $nom_demandeur = htmlspecialchars($_POST['nom_demandeur']);
    $quantite = htmlspecialchars($_POST['quantite']);
    $email_demandeur = htmlspecialchars($_POST['email_demandeur']);
    $date_debut = htmlspecialchars($_POST['date_debut']);
    $date_fin = htmlspecialchars($_POST['date_fin']);
var_dump($_POST['bien_id']);
    if (empty($bien_id) || empty($nom_demandeur) || empty($email_demandeur) || empty($date_debut) || empty($date_fin)||empty($quantite)) {
        echo "Veuillez remplir tous les champs.";
        header("Location: biens.php#demande_location");
        exit();
    }

    if (!filter_var($email_demandeur, FILTER_VALIDATE_EMAIL)) {
        echo "L'adresse email n'est pas valide.";
        exit();
    }

    $date_debut_obj = DateTime::createFromFormat('Y-m-d', $date_debut);
    $date_fin_obj = DateTime::createFromFormat('Y-m-d', $date_fin);

    if (!$date_debut_obj || !$date_fin_obj || $date_debut_obj > $date_fin_obj) {
        echo "Les dates ne sont pas valides ou la date de début est après la date de fin.";
        exit();
    }

    try {
        $sql = "INSERT INTO `demandes_location` (`bien_id`, `nom_demandeur`,`quantite`, `email_demandeur`, `date_debut`, `date_fin`) VALUES (:bien_id, :nom_demandeur,:quantite, :email_demandeur, :date_debut, :date_fin)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':bien_id', $bien_id, PDO::PARAM_INT);
        $stmt->bindParam(':nom_demandeur', $nom_demandeur, PDO::PARAM_STR);
        $stmt->bindParam(':quantite', $quantite, PDO::PARAM_STR);
        $stmt->bindParam(':email_demandeur', $email_demandeur, PDO::PARAM_STR);
        $stmt->bindParam(':date_debut', $date_debut, PDO::PARAM_STR);
        $stmt->bindParam(':date_fin', $date_fin, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $msg = "Votre demande de location a été envoyée avec succès.";
            header("Location: biens.php");
            exit();
        } else {
            $errormsg = "Erreur lors de l'envoi de la demande de location.";
            header ("loaction:biens.php");
            exit();
        }
        if (!headers_sent()) {
            header("Location: biens.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>