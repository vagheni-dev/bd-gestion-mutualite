<?php
session_start();
require_once("connexion/connexion.php");

// Vérifier si $pdo est défini
if (!isset($pdo)) {
    die("Erreur de connexion à la base de données.");
}

// Récupérer les biens de la mutualité
$stmt = $pdo->prepare("SELECT * FROM `biens`");
$stmt->execute();
$biens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gérer les demandes de location
if (isset($_POST['demande_location'])) {
    $bien_id = htmlspecialchars($_POST['bien_id']);
    $nom_demandeur = htmlspecialchars($_POST['nom_demandeur']);
    $email_demandeur = htmlspecialchars($_POST['email_demandeur']);
    $date_debut = htmlspecialchars($_POST['date_debut']);
    $date_fin = htmlspecialchars($_POST['date_fin']);

    try {
        $sql = "INSERT INTO `demandes_location` (`bien_id`, `nom_demandeur`, `email_demandeur`, `date_debut`, `date_fin`) VALUES (:bien_id, :nom_demandeur, :email_demandeur, :date_debut, :date_fin)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':bien_id', $bien_id, PDO::PARAM_INT);
        $stmt->bindParam(':nom_demandeur', $nom_demandeur, PDO::PARAM_STR);
        $stmt->bindParam(':email_demandeur', $email_demandeur, PDO::PARAM_STR);
        $stmt->bindParam(':date_debut', $date_debut, PDO::PARAM_STR);
        $stmt->bindParam(':date_fin', $date_fin, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: biens.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
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

    <title>Gestion des Biens</title>

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
                                    <h5 class="card-title">Liste des Biens</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-responsive-xl">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Nom</th>
                                                <th>Description</th>
                                                <th>Adresse</th>
                                                <th>Prix de Location</th>
                                                <th>Disponibilité</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($biens as $bien) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($bien['nom']); ?></td>
                                                <td><?php echo htmlspecialchars($bien['description']); ?></td>
                                                <td><?php echo htmlspecialchars($bien['adresse']); ?></td>
                                                <td><?php echo htmlspecialchars($bien['prix_location']); ?></td>
                                                <td><?php echo $bien['disponible'] ? 'Disponible' : 'Indisponible'; ?></td>
                                                <td>
                                                    <?php if ($bien['disponible']) { ?>
                                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#demandeLocationModal" data-id="<?php echo htmlspecialchars($bien['id']); ?>">Demander Location</button>
                                                    <?php } else { ?>
                                                    <button class="btn btn-secondary btn-sm" disabled>Indisponible</button>
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

    <!-- Modal -->
    <div class="modal fade" id="demandeLocationModal" tabindex="-1" aria-labelledby="demandeLocationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demandeLocationModalLabel">Demande de Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="biens.php" method="post">
                        <input type="hidden" name="bien_id" id="bien_id">
                        <div class="mb-3">
                            <label for="nom_demandeur" class="form-label">Nom</label>
                            <input name="nom_demandeur" type="text" class="form-control" id="nom_demandeur" placeholder="Nom">
                        </div>
                        <div class="mb-3">
                            <label for="email_demandeur" class="form-label">Email</label>
                            <input name="email_demandeur" type="email" class="form-control" id="email_demandeur" placeholder="Email">
                        </div>
                        <div class="mb-3">
                            <label for="date_debut" class="form-label">Date de Début</label>
                            <input name="date_debut" type="date" class="form-control" id="date_debut">
                        </div>
                        <div class="mb-3">
                            <label for="date_fin" class="form-label">Date de Fin</label>
                            <input name="date_fin" type="date" class="form-control" id="date_fin">
                        </div>
                        <button type="submit" class="btn btn-primary" name="demande_location">Envoyer la Demande</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="indexs.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const demandeButtons = document.querySelectorAll('.btn-primary');
            demandeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const bienId = button.getAttribute('data-id');
                    document.getElementById('bien_id').value = bienId;
                });
            });
        });
    </script>
</body>
</html>