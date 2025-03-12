<?php
session_start();
require_once("../connexion/connexion.php");
if (isset($_POST['action'])) {
    $demande_id = htmlspecialchars($_POST['demande_id']);
    $action = htmlspecialchars($_POST['action']);

    try {
        if ($action == 'approuver') {
            $sql = "UPDATE `demandes_location` SET `statut` = 'Approuvée' WHERE `id` = :demande_id";
        } elseif ($action == 'rejeter') {
            $sql = "UPDATE `demandes_location` SET `statut` = 'Rejetée' WHERE `id` = :demande_id";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':demande_id', $demande_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: demandes.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

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
    if (empty($bien_id) || empty($nom_demandeur) || empty($email_demandeur) || empty($date_debut) || empty($date_fin) || empty($quantite)) {
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
        $sql = "INSERT INTO `demandes_location` (`bien_id`, `nom_demandeur`, `quantite`, `email_demandeur`, `date_debut`, `date_fin`) VALUES (:bien_id, :nom_demandeur, :quantite, :email_demandeur, :date_debut, :date_fin)";
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
            header("Location: biens.php");
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

// Afficher toutes les demandes de location
try {
    $sql = "SELECT * FROM `demandes_location`";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demandes de Location</title>
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
<?php include_once("entete.php"); ?>
    <div class="container">
        <h1 class="my-4">Liste des Demandes de Location</h1>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Bien ID</th>
                    <th>Nom du Demandeur</th>
                    <th>Quantité</th>
                    <th>Email du Demandeur</th>
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($demandes as $demande): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($demande['id']); ?></td>
                        <td><?php echo htmlspecialchars($demande['bien_id']); ?></td>
                        <td><?php echo htmlspecialchars($demande['nom_demandeur']); ?></td>
                        <td><?php echo htmlspecialchars($demande['quantite']); ?></td>
                        <td><?php echo htmlspecialchars($demande['email_demandeur']); ?></td>
                        <td><?php echo htmlspecialchars($demande['date_debut']); ?></td>
                        <td><?php echo htmlspecialchars($demande['date_fin']); ?></td>
                        <td><?php echo htmlspecialchars($demande['statut']); ?></td>
                        <td>
                        <?php if ($demande['statut'] == 'En attente') { ?>
                                <form action="demandes.php" method="post" style="display:inline;">
                                     <input type="hidden" name="demande_id" value="<?php echo htmlspecialchars($demande['id']); ?>">
                                        <button type="submit" name="action" value="approuver" class="btn btn-success btn-sm">Approuver</button>
                                    </form>
                                <form action="demandes.php" method="post" style="display:inline;">
                                    <input type="hidden" name="demande_id" value="<?php echo htmlspecialchars($demande['id']); ?>">
                                    <button type="submit" name="action" value="rejeter" class="btn btn-danger btn-sm">Rejeter</button>
                                </form>
                             <?php } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php 
        require_once("../footer.php");
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="indexs.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>