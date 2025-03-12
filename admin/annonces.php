<?php
session_start();
require_once("connexion/connexion.php");

// Vérifier si $pdo est défini
if (!isset($pdo)) {
    die("Erreur de connexion à la base de données.");
}

// Gérer l'approbation ou le rejet des annonces
if (isset($_POST['action'])) {
    $annonce_id = htmlspecialchars($_POST['annonce_id']);
    $action = htmlspecialchars($_POST['action']);
    $message = htmlspecialchars($_POST['message']);

    try {
        if ($action == 'approuver') {
            $sql = "UPDATE `annonces` SET `statut` = 'Approuvée', `message` = :message WHERE `id` = :annonce_id";
        } elseif ($action == 'rejeter') {
            $sql = "UPDATE `annonces` SET `statut` = 'Rejetée', `message` = :message WHERE `id` = :annonce_id";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':annonce_id', $annonce_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();

        // Enregistrer le message dans la table messages
        $sql = "INSERT INTO `messages` (`annonce_id`, `message`) VALUES (:annonce_id, :message)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':annonce_id', $annonce_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: annonces.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// Récupérer les annonces
$stmt = $pdo->prepare("SELECT a.*, b.nom AS bien_nom FROM `annonces` a JOIN `biens` b ON a.bien_id = b.id");
$stmt->execute();
$annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Gestion des Annonces</title>

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
                                    <h5 class="card-title">Liste des Annonces</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-responsive-xl">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Bien</th>
                                                <th>Nom du Demandeur</th>
                                                <th>Email du Demandeur</th>
                                                <th>Date de Début</th>
                                                <th>Date de Fin</th>
                                                <th>Quantité</th>
                                                <th>Statut</th>
                                                <th>Message</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($annonces as $annonce) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($annonce['bien_nom']); ?></td>
                                                <td><?php echo htmlspecialchars($annonce['nom_demandeur']); ?></td>
                                                <td><?php echo htmlspecialchars($annonce['email_demandeur']); ?></td>
                                                <td><?php echo htmlspecialchars($annonce['date_debut']); ?></td>
                                                <td><?php echo htmlspecialchars($annonce['date_fin']); ?></td>
                                                <td><?php echo htmlspecialchars($annonce['quantite']); ?></td>
                                                <td><?php echo htmlspecialchars($annonce['statut']); ?></td>
                                                <td><?php echo htmlspecialchars($annonce['message']); ?></td>
                                                <td>
                                                    <?php if ($annonce['statut'] == 'En attente') { ?>
                                                    <form action="annonces.php" method="post" style="display:inline;">
                                                        <input type="hidden" name="annonce_id" value="<?php echo htmlspecialchars($annonce['id']); ?>">
                                                        <textarea name="message" class="form-control mb-2" placeholder="Laisser un message"></textarea>
                                                        <button type="submit" name="action" value="approuver" class="btn btn-success btn-sm">Approuver</button>
                                                    </form>
                                                    <form action="annonces.php" method="post" style="display:inline;">
                                                        <input type="hidden" name="annonce_id" value="<?php echo htmlspecialchars($annonce['id']); ?>">
                                                        <textarea name="message" class="form-control mb-2" placeholder="Laisser un message"></textarea>
                                                        <button type="submit" name="action" value="rejeter" class="btn btn-danger btn-sm">Rejeter</button>
                                                    </form>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
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
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>
