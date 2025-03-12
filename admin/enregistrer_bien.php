<?php
session_start();
require_once("../connexion/connexion.php");

// Vérifier si $pdo est défini
if (!isset($pdo)) {
    die("Erreur de connexion à la base de données.");
}

// Récupérer les catégories
$stmt = $pdo->prepare("SELECT * FROM `categories`");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gérer l'enregistrement de bien
if (isset($_POST['enregistrer_bien'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $prix_location = htmlspecialchars($_POST['prix_location']);
    $categorie_id = htmlspecialchars($_POST['categorie_id']);

    // Vérification et enregistrement de l'image
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    $uploadOk = 1;

    // Vérifier si le fichier est une image
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "Le fichier n'est pas une image.";
        $uploadOk = 0;
    }

     /* Vérifier la taille du fichier
     if ($_FILES["image"]["size"] > 1000000) {
        echo "Désolé, votre fichier est trop volumineux.";
        $uploadOk = 0;
    } */

    // Autoriser certains formats de fichier
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        $uploadOk = 0;
    }

    // Vérifier si $uploadOk est défini à 0 par une erreur
    if ($uploadOk == 0) {
        echo "Désolé, votre fichier n'a pas été téléchargé.";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = basename($_FILES["image"]["name"]);
        } else {
            echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
        }
    }

    if ($uploadOk == 1) {
        try {
            $sql = "INSERT INTO `biens` (`nom`, `description`, `adresse`, `prix_location`, `categorie`, `image`) VALUES (:nom, :description, :adresse, :prix_location, :categorie_id, :image)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':adresse', $adresse, PDO::PARAM_STR);
            $stmt->bindParam(':prix_location', $prix_location, PDO::PARAM_STR);
            $stmt->bindParam(':categorie_id', $categorie_id, PDO::PARAM_INT);
            $stmt->bindParam(':image', $image, PDO::PARAM_STR);
            $stmt->execute();

            header("Location: biens.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Enregistrer un Bien</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body>
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php include("entete.php"); ?>
                <?php
                if (isset($_SESSION['username'])) {
                    echo "<div class='text-success'><center>Welcome, " . htmlspecialchars($_SESSION['username']) . "!</center></div>";
                }
                if (isset($msg) && $msg != "") {
                    echo "<div class='text-success'><center>" . htmlspecialchars($msg) . "</center></div>";
                }
                if (isset($errormsg) && $errormsg != "") {
                    echo "<div class='text-danger'><center>" . htmlspecialchars($errormsg) . "</center></div>";
                }
                ?>

                <div class="container mt-5">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Enregistrer un Bien</h5>
                                </div>
                                <div class="card-body">
                                    <form action="enregistrer_bien.php" method="post" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="nom" class="form-label">Nom</label>
                                            <input name="nom" type="text" class="form-control" id="nom" placeholder="Nom">
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea name="description" class="form-control" id="description" placeholder="Description"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="adresse" class="form-label">Adresse</label>
                                            <input name="adresse" type="text" class="form-control" id="adresse" placeholder="Adresse">
                                        </div>
                                        <div class="mb-3">
                                            <label for="prix_location" class="form-label">Prix de Location</label>
                                            <input name="prix_location" type="text" class="form-control" id="prix_location" placeholder="Prix de Location">
                                        </div>
                                        <div class="mb-3">
                                            <label for="categorie_id" class="form-label">Catégorie</label>
                                            <select name="categorie_id" class="form-control" id="categorie_id">
                                                <?php foreach($categories as $categorie) { ?>
                                                <option value="<?php echo htmlspecialchars($categorie['id']); ?>"><?php echo htmlspecialchars($categorie['nom']); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Image</label>
                                            <input name="image" type="file" class="form-control" id="image">
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="enregistrer_bien">Enregistrer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->
    <?php 
        require_once("../footer.php");
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../indexs.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>