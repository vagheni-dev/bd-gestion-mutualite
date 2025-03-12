<?php
session_start();
require_once("../connexion/connexion.php");

// Vérifier si $pdo est défini
if (!isset($pdo)) {
    die("Erreur de connexion à la base de données.");
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the request ID and refusal reason from the POST data
    $request_id = htmlspecialchars($_POST['request_id']);
    $refusal_reason = htmlspecialchars($_POST['refusal_reason']);

    try {
        // Update the request status to 'refused' in the database
        $query = "UPDATE demandes_location SET statut = 'refused', WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $request_id, PDO::PARAM_INT);
        $stmt->execute();

        // Get the matricule of the user who sent the request
        $query = "SELECT matricule FROM user WHERE id = (SELECT user_id FROM demandes_location WHERE id = :id)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $request_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user_matricule = $user['matricule'];

            // Send a message to the user with the refusal reason
            $message = "Votre demande a été refusée pour la raison suivante : " . $refusal_reason;
            sendMessageToUser($user_matricule, $message);
        }

        // Redirect to the requests page with a success message
        header('Location: demandes.php?message=Demande refusée avec succès');
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de la mise à jour de la demande : " . $e->getMessage());
    }
}

function sendMessageToUser($matricule, $message) {
    // Implement the function to send a message to the user by matricule
    // This could be an SMS, a notification in the system, etc.
    // For example:
    // sendSMS($matricule, $message);
    // or
    // saveNotification($matricule, $message);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refuser Demande</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <form action="refuser_demande.php" method="POST">
            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
            <div class="form-group">
                <label for="refusal_reason">Raison du refus :</label>
                <textarea name="refusal_reason" id="refusal_reason" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-danger">Refuser la demande</button>
        </form>
    </div>

    <?php 
        require_once("../footer.php");
         ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>