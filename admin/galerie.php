<?php
session_start();
require_once("../connexion/connexion.php");

// Vérifier si $pdo est défini
if (!isset($pdo)) {
    die("Erreur de connexion à la base de données.");
}

// Gérer l'ajout d'une image
if (isset($_POST['ajouter_image'])) {
    $description = htmlspecialchars($_POST['description']);

    // Vérification et enregistrement de l'image
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;

    // Vérifier si le fichier est une image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "Le fichier n'est pas une image.";
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
            $sql = "INSERT INTO `image` (`image`, `description`) VALUES (:image, :description)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':image', $image, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->execute();

            header("Location: galerie.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
    }
}
// Gérer la modification d'une image
if (isset($_POST['modifier_image'])) {
    $image_id = $_POST['image_id'];
    $description = htmlspecialchars($_POST['description']);
    $image = null;

    // Vérification et enregistrement de la nouvelle image si elle est téléchargée
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;

        // Vérifier si le fichier est une image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "Le fichier n'est pas une image.";
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
    }

    try {
        if ($image) {
            $sql = "UPDATE `image` SET `image` = :image, `description` = :description WHERE `id` = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        } else {
            $sql = "UPDATE `image` SET `description` = :description WHERE `id` = :id";
            $stmt = $pdo->prepare($sql);
        }
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':id', $image_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: galerie.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// Gérer la suppression d'une image
if (isset($_POST['supprimer_image'])) {
    $image_id = $_POST['image_id'];

    try {
        // Supprimer l'image du dossier
        $stmt = $pdo->prepare("SELECT `image` FROM `image` WHERE `id` = :id");
        $stmt->bindParam(':id', $image_id, PDO::PARAM_INT);
        $stmt->execute();
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image && file_exists("images/" . $image['image'])) {
            unlink("images/" . $image['image']);
        }

        // Supprimer l'image de la base de données
        $sql = "DELETE FROM `image` WHERE `id` = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $image_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: galerie.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// Récupérer les images de la galerie
$stmt = $pdo->prepare("SELECT * FROM `image` ORDER BY `id` DESC");
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerie</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body>
    <?php include("entete.php"); ?>
    <div class="container mt-5">
        <h2>Galerie</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addImageModal">Ajouter une Image</button>
        <div class="row">
            <?php foreach($images as $image) { ?>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card">
                    <img src="images/<?php echo htmlspecialchars($image['image']); ?>" class="card-img-top" alt="Image">
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($image['description']); ?></p>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editImageModal<?php echo $image['id']; ?>">Modifier</button>
                        <form action="galerie.php" method="post" class="d-inline">
                            <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                            <button type="submit" class="btn btn-danger" name="supprimer_image">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Image Modal -->
            <div class="modal fade" id="editImageModal<?php echo $image['id']; ?>" tabindex="-1" aria-labelledby="editImageModalLabel<?php echo $image['id']; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editImageModalLabel<?php echo $image['id']; ?>">Modifier l'Image</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="galerie.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                <div class="mb-3">
                                    <label for="description<?php echo $image['id']; ?>" class="form-label">Description</label>
                                    <textarea class="form-control" id="description<?php echo $image['id']; ?>" name="description" required><?php echo htmlspecialchars($image['description']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="image<?php echo $image['id']; ?>" class="form-label">Image</label>
                                    <input type="file" class="form-control" id="image<?php echo $image['id']; ?>" name="image">
                                </div>
                                <button type="submit" class="btn btn-primary" name="modifier_image">Modifier</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addImageModal" tabindex="-1" aria-labelledby="addImageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addImageModalLabel">Ajouter une Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="galerie.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="ajouter_image">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php 
        require_once("../footer.php");
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="indexs.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>
