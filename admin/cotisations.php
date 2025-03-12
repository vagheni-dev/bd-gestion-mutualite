<?php
session_start();
require_once("../connexion/connexion.php");

// Vérifier si $pdo est défini
if (!isset($pdo)) {
    die("Erreur de connexion à la base de données.");
}

// Récupérer les cotisations des membres
$stmt = $pdo->prepare("
    SELECT 
        c.matricule, 
        u.nom, 
        c.type_cotisation, 
        SUM(c.montant) as montant, 
        MAX(c.date_paiement) as date_paiement 
    FROM 
        cotisations c 
    JOIN user u
    ON 
        c.matricule = u.matricule 
    GROUP BY 
        c.matricule, u.nom, c.type_cotisation
");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les membres pour le select
$stmt_members = $pdo->prepare("SELECT matricule, nom FROM `user`");
$stmt_members->execute();
$members = $stmt_members->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Suivi des Cotisations</title>

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
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title">Suivi des Cotisations</h5>
                                    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addTypeCotisationModal">Ajouter Type de Cotisation</button>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCotisationModal">Ajouter la cotisation</button>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-responsive-xl">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Matricule</th>
                                                <th>Nom</th>
                                                <th>Type de Cotisation</th>
                                                <th>Montant</th>
                                                <th>Date de Paiement</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($result as $row) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['matricule']); ?></td>
                                                <td><?php echo htmlspecialchars($row['nom']); ?></td>
                                                <td><?php echo htmlspecialchars($row['type_cotisation']); ?></td>
                                                <td><?php echo htmlspecialchars($row['montant']); ?></td>
                                                <td><?php echo htmlspecialchars($row['date_paiement']); ?></td>
                                                <td>
                                                    <a href="fiche_paiement.php?matricule=<?php echo htmlspecialchars($row['matricule']); ?>" class="btn btn-info btn-sm">voir fiche</a>
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
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['type_cotisation'])) {
        $type_cotisation = $_POST['type_cotisation'];
        $stmt_add_type = $pdo->prepare("INSERT INTO typeco(type_cot) VALUES (:type_cotisation)");
        $stmt_add_type->bindParam(':type_cotisation', $type_cotisation);
        if ($stmt_add_type->execute()) {
            $msg = "Type de cotisation ajouté avec succès.";
        } else {
            $errormsg = "Erreur lors de l'ajout du type de cotisation.";
        }
    }
    ?>
    <div class="modal fade" id="addTypeCotisationModal" tabindex="-1" aria-labelledby="addTypeCotisationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="cotisations.php" method="post">
                        <div class="mb-3">
                            <label for="type_cotisation" class="form-label">Type de Cotisation</label>
                            <input type="text" class="form-control" id="type_cotisation" name="type_cotisation" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addCotisationModal" tabindex="-1" aria-labelledby="addCotisationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCotisationModalLabel">Ajouter une Cotisation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="ajouter_cotisation.php" method="post">
                        <div class="mb-3">
                            <label for="matricule" class="form-label">Matricule</label>
                            <select class="form-select" id="matricule" name="matricule" required>
                                <option value="">Sélectionner un matricule</option>
                                <?php foreach($members as $member) { ?>
                                <option value="<?php echo htmlspecialchars($member['matricule']); ?>"><?php echo htmlspecialchars($member['matricule']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="type_cotisation" class="form-label">Type de Cotisation</label>
                            <select class="form-select" id="type_cotisation" name="type_cotisation" required>
                                <option value="">Sélectionner un type de cotisation</option>
                                <?php
                                $stmt_types = $pdo->prepare("SELECT DISTINCT type_cot FROM typeco");
                                $stmt_types->execute();
                                $types = $stmt_types->fetchAll(PDO::FETCH_ASSOC);
                                foreach($types as $type) { ?>
                                    <option value="<?php echo htmlspecialchars($type['type_cot']); ?>"><?php echo htmlspecialchars($type['type_cot']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="montant" class="form-label">Montant</label>
                            <input type="number" class="form-control" id="montant" name="montant" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Ajouter</button>
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
    <script>
        $(document).ready(function() {
            $('#matricule').change(function() {
                var matricule = $(this).val();
                var members = <?php echo json_encode($members); ?>;
                var selectedMember = members.find(member => member.matricule === matricule);
                if (selectedMember) {
                    $('#nom').val(selectedMember.nom);
                } else {
                    $('#nom').val('');
                }
            });
        });
    </script>
</body>
</html>