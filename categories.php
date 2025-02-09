<?php
session_start();
require_once("connexion/connexion.php");

// Vérifier si $pdo est défini
if (!isset($pdo)) {
    die("Erreur de connexion à la base de données.");
}

// Gérer l'ajout de catégorie
if (isset($_POST['ajouter_categorie'])) {
    $nom = htmlspecialchars($_POST['nom']);

    try {
        $sql = "INSERT INTO `categories` (`nom`) VALUES (:nom)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: categories.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// Récupérer les catégories
$stmt = $pdo->prepare("SELECT * FROM `categories`");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Gestion des Catégories</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body>
<?php include("navigation.php");?>
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
                                    <h5 class="card-title">Liste des Catégories</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-responsive-xl">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Nom</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($categories as $categorie) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($categorie['nom']); ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card mt-5">
                                <div class="card-header">
                                    <h5 class="card-title">Ajouter une Catégorie</h5>
                                </div>
                                <div class="card-body">
                                    <form action="categories.php" method="post">
                                        <div class="mb-3">
                                            <label for="nom" class="form-label">Nom</label>
                                            <input name="nom" type="text" class="form-control" id="nom" placeholder="Nom">
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="ajouter_categorie">Ajouter</button>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="indexs.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>