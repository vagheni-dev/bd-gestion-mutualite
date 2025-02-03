<?php /*
require_once("connexion/connexion.php");

// Get the ID from the GET request
if (isset($_GET["mod"])) {
    $id =htmlspecialchars( $_GET['mod']);

    // Fetch the record from the database using PDO
    try {
        $sql = "SELECT * FROM `admin` WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Output data of each row
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "No record found";
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'élément</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Modifier l'élément</h2>
        <form action="#" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
            <div class="form-group">
                <label for="name">Nom:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($row['nom']); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
            </div>
            <button type="submit" class="btn btn-primary" name="update">Modifier</button>
            <a href="admin.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> */?>

<?php

if (isset($_GET['update'])) {
    require_once("connexion/connexion.php");
    $id = htmlspecialchars($_POST['id']);
    $name = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);

  
    try {
        $sql = "UPDATE `admin` SET `nom` = :name, `email` = :email WHERE `id` = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

       
        header("Location: admin.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    echo "Invalid request";
    exit();
}
?>
