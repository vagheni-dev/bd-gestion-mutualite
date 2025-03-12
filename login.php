<?php
session_start();
require_once("connexion/connexion.php");

if (isset($_POST['login'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    try {
        $sql = "SELECT * FROM `user` WHERE `matricule` = :matricule";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':matricule', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['postnom'] = $user['postnom'];
            $_SESSION['matricule'] = $user['matricule'];
            $_SESSION['image'] = $user['photos'];
            $_SESSION['nom'] = $user['nom'];
            if ($user['fonction'] == 'admin') {
                $_SESSION['image'] = $user['photos'];
                $_SESSION['role'] = 'admin';
                header("Location: admin/admin.php");
                exit();
            }
            else if ($user['fonction'] == 'user') {
                $_SESSION['role'] = 'user';
                header("Location: index.php");
                exit();
            }
            else if ($user['fonction'] == 'superadmin') {
                $_SESSION['role'] = 'superadmin';
                header("Location: superadmin.php");
                exit();
            }
           
        } else {
            $errormsg = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
    <div class="container">
        <h2 style="text-align: center;">Connexion</h2>
        <?php if (isset($errormsg)) { echo "<div class='text-danger'>$errormsg</div>"; } ?>
        <form action="login.php" method="post" style="max-width: 400px; margin: auto;">
            <div class="mb-3">
            <label for="username" class="form-label">Nom d'utilisateur</label>
            <input type="text" name="username" class="form-control" id="username" required>
            </div>
            <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="login">Se connecter</button>
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <button class="btn btn-secondary" onclick="goBack()">Retour</button>
        </div>

        <script>
        function goBack() {
            window.history.back();
        }
        </script>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="indexs.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>