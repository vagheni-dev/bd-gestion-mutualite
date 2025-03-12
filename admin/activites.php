<?php
session_start();
require_once("../connexion/connexion.php");

// Vérifier si $pdo est défini
if (!isset($pdo)) {
    die("Erreur de connexion à la base de données.");
}

// Gérer l'ajout d'une activité
if (isset($_POST['ajouter_activite'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);

    // Vérification et enregistrement de l'image
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    $uploadOk = 1;

    // Vérifier si le fichier est une image
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
            $sql = "INSERT INTO `activite` (`nom`, `description`, `image`) VALUES (:nom, :description, :image)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':image', $image, PDO::PARAM_STR);
            $stmt->execute();

            header("Location: activites.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
    }
}

// Gérer la suppression d'une activité
if (isset($_GET['supprimer'])) {
    $id = htmlspecialchars($_GET['supprimer']);

    try {
        $sql = "DELETE FROM `activite` WHERE `id` = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: activites.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// Récupérer les activités
$stmt = $pdo->prepare("SELECT * FROM `activite` ORDER BY `id` DESC");
$stmt->execute();
$activites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Activités</title>
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
        <h2>Gestion des Activités</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addActiviteModal">Ajouter une Activité</button>
        <table class="table table-bordered table-responsive-xl">
            <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($activites as $activite) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($activite['nom']); ?></td>
                    <td><?php echo htmlspecialchars($activite['description']); ?></td>
                    <td><img src="images/<?php echo htmlspecialchars($activite['image']); ?>" alt="Image" width="100"></td>
                    <td>
                        <a href="modifier_activite.php?id=<?php echo htmlspecialchars($activite['id']); ?>" class="btn btn-primary btn-sm">Modifier</a>
                    <td>
                        <a href="activites.php?supprimer=<?php echo htmlspecialchars($activite['id']); ?>" class="btn btn-danger btn-sm">Supprimer</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addActiviteModal" tabindex="-1" aria-labelledby="addActiviteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addActiviteModalLabel">Ajouter une Activité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="activites.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="ajouter_activite">Ajouter</button>
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