<?php
session_start();
require_once("../connexion/connexion.php");

function generatePassword($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomPassword;
}

if (isset($_POST['envoie'])) {
    
    $nom = htmlspecialchars($_POST['nom']);
    $postnom = htmlspecialchars($_POST['postnom']);
    $fonction = htmlspecialchars($_POST['fonction']);
    $email = htmlspecialchars($_POST['email']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $contact = htmlspecialchars($_POST['contact']);
    $genre = htmlspecialchars($_POST['genre']);
    $matricule = substr($nom, 0, 2) . substr($postnom, 0, 2) . date('YmdHis') . substr($fonction, 0, 2);
    // Générer un mot de passe aléatoire
    $password = generatePassword();
    // Hacher le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Vérification et enregistrement de l'image
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["photos"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["photos"]["tmp_name"]);
    $uploadOk = 1;

    // Vérifier si le fichier est une image
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "Le fichier n'est pas une image.";
        $uploadOk = 0;
    }

    /* Vérifier la taille du fichier
    if ($_FILES["photos"]["size"] > 1000000) {
        echo "Désolé, votre fichier est trop volumineux.";
        $uploadOk = 0;
    }*/

    // Autoriser certains formats de fichier
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        $uploadOk = 0;
    }

    // Vérifier si $uploadOk est défini à 0 par une erreur
    if ($uploadOk == 0) {
        echo "Désolé, votre fichier n'a pas été téléchargé.";
    } else {
        if (move_uploaded_file($_FILES["photos"]["tmp_name"], $target_file)) {
            $photos = basename($_FILES["photos"]["name"]);
        } else {
            echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
        }
    }


    if ($uploadOk == 1) {
        try {
            $sql = "INSERT INTO `user` (`matricule`, `nom`, `postnom`, `fonction`, `mail`, `Adresse`, `contact`, `genre`, `photos`, `password`,`pwd`) VALUES (:matricule, :nom, :postnom, :fonction, :email, :adresse, :contact, :genre, :photos, :password, :pwd)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':postnom', $postnom, PDO::PARAM_STR);
            $stmt->bindParam(':fonction', $fonction, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':adresse', $adresse, PDO::PARAM_STR);
            $stmt->bindParam(':photos', $photos, PDO::PARAM_STR);
            $stmt->bindParam(':contact', $contact, PDO::PARAM_STR);
            $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':pwd', $password, PDO::PARAM_STR);
            $stmt->execute();

            echo "<script>
                alert('Votre mot de passe est: $password');
                function copyToClipboard(text) {
                    var dummy = document.createElement('textarea');
                    document.body.appendChild(dummy);
                    dummy.value = text;
                    dummy.select();
                    document.execCommand('copy');
                    document.body.removeChild(dummy);
                }
                copyToClipboard('$password');
                window.location.href = 'admin.php';
            </script>";
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
    }
}
?>