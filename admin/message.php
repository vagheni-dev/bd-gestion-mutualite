<?php
// Connexion à la base de données
session_start();
require_once("../connexion/connexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];
    $matricule = $_SESSION['matricule'];

    $sql = "INSERT INTO `communique` (`message`, matricule) VALUES (:message, :matricule)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':matricule', $matricule);

    if ($stmt->execute()) {
        header("location:message.php");
        exit();
    } else {
        $errormsg = "Erreur: " . $stmt->errorInfo()[2];
    }
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] == 'delete') {
        $sql = "DELETE FROM communique WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            header("location:message.php");
            exit();
        } else {
            $errormsg = "Erreur lors de la suppression du message.";
        }
    } elseif ($_GET['action'] == 'edit' && $_SERVER["REQUEST_METHOD"] == "POST") {
        $message = $_POST['message'];
        $sql = "UPDATE communique SET message = :message WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            header("location:message.php");
            exit();
        } else {
            $errormsg = "Erreur lors de la mise à jour du message.";
        }
    }
}

// Fetch messages from the database
$sql = "SELECT id, `message` FROM communique WHERE matricule = (SELECT matricule FROM user WHERE fonction = 'admin')";
$result = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un communiqué</title>
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Assurez-vous que ce fichier CSS existe et correspond au design des autres pages -->

</head>
<body>
<?php include("entete.php"); ?>
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column align-items-center">
            <div id="content" class="w-100 text-center">
                
                <br>
                <br>
                <br>
                <div class="row justify-content-center">
                    <?php
                    if (isset($_SESSION['matricule'])) {
                        echo "<div class='text-success'><center>Bienvenue, " . htmlspecialchars($_SESSION['nom']).'  ' .htmlspecialchars($_SESSION['postnom']) . "!</center></div>";
                    }
                    if (isset($msg) && $msg != "") {
                        echo "<div class='text-success'><center>" . htmlspecialchars($msg) . "</center></div>";
                    }
                    if (isset($errormsg) && $errormsg != "") {
                        echo "<div class='text-danger'><center>" . htmlspecialchars($errormsg) . "</center></div>";
                    }
                    ?>
                </div>
            </div>
        </div>
 
    </div>

    <div class="container mt-5 text-center d-flex flex-column">
        <h1 class="mb-4">Ajouter un communiqué</h1>
        <form method="post" action="message.php">
            <div class="form-group">
                <label for="message">Communiqué:</label>
                <textarea id="message" name="message" class="form-control" rows="4" cols="50"></textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Ajouter</button>
        </form>

        <h2 class="mt-5">Communiqués</h2>
        <div class="messages mt-3">
            <?php
            if ($result->rowCount() > 0) {
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='alert alert-secondary d-flex justify-content-between align-items-center' role='alert'>";
                echo htmlspecialchars($row['message']);
                echo "<div>";
                echo "<button class='btn btn-warning btn-sm mx-1' data-bs-toggle='modal' data-bs-target='#editModal' data-id='" . $row['id'] . "' data-message='" . htmlspecialchars($row['message']) . "'>Modifier</button>";
                echo "<a href='message.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger btn-sm mx-1' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce message?\");'>Supprimer</a>";
                echo "</div>";
                echo "</div>";
            }
            } else {
            echo "<p class='alert alert-info'>Aucun communiqué trouvé.</p>";
            }
            ?>
        </div>
    </div>
    <!-- Modal for editing message -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="message.php?action=edit&id=" id="editForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Modifier le communiqué</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editMessage">Communiqué:</label>
                            <textarea id="editMessage" name="message" class="form-control" rows="4" cols="50"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <?php 
        require_once("../footer.php");
     ?>

<script>
        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var message = button.getAttribute('data-message');

            var form = document.getElementById('editForm');
            form.action = 'message.php?action=edit&id=' + id;

            var editMessage = document.getElementById('editMessage');
            editMessage.value = message;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="indexs.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>