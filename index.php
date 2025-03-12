<?php
require_once('connexion/connexion.php');
 $sql = "SELECT `message` FROM communique  WHERE matricule = (SELECT matricule FROM user WHERE fonction ='admin')";
$result = $pdo->query($sql);
?> 
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de l'Association</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="./css/furaha.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php require_once('navigation.php');?>  
    <br>
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

    <?php
    $sql_carousel = "SELECT `image`, `description` FROM image ORDER BY id ASC LIMIT 3";
    $result_carousel = $pdo->query($sql_carousel);
    $active = true;
    ?>
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" style="max-height: 500px; overflow: hidden;">
        <div class="carousel-indicators">
            <?php for ($i = 0; $i < $result_carousel->rowCount(); $i++): ?>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?php echo $i; ?>" class="<?php echo $i === 0 ? 'active' : ''; ?>" aria-current="<?php echo $i === 0 ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $i + 1; ?>"></button>
            <?php endfor; ?>
        </div>
        <div class="carousel-inner">
            <?php while ($row = $result_carousel->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="carousel-item <?php echo $active ? 'active' : ''; ?>">
                    <img src="admin/images/<?php echo htmlspecialchars($row['image']); ?>" class="d-block w-100" alt="..." style="max-height: 500px; object-fit: cover;">
                    <div class="carousel-caption d-none d-md-block">
                        <h5><?php echo htmlspecialchars($row['description']); ?></h5>
                    </div>
                </div>
                <?php $active = false; ?>
            <?php endwhile; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
 
    
    <div class="container">
        <h1>Gestion de l'Association</h1>
        
        <h2>Nos activités</h2>
        
        <?php
        $sql_activites = "SELECT `nom`, `description`, `image` FROM activite ORDER BY id DESC LIMIT 3";
        $result_activites = $pdo->query($sql_activites);
        ?>
        <div class="cards">
            <?php
            if ($result_activites->rowCount() > 0) {
                while($row = $result_activites->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="card">';
                    echo '<img src="admin/images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['nom']) . '">';
                    echo '<div class="card-container">';
                    echo '<h4>' . htmlspecialchars($row['nom']) . '</h4>';
                    echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Aucune activité trouvée</p>';
            }
            ?>
        </div>
    
        <h2>ANNONCES</h2>
        <?php
            if ($result->rowCount() > 0) {
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="grand">';
                echo '<div class="TEXT">';
                echo "<h3>Communiqué</h3>";
                echo "<p>" . htmlspecialchars($row['message']) . "</p>";
                echo '</div>';
                echo '</div>';
            }
            } else {
            echo "<h3>Communiqué</h3>";
            echo "<p>Aucun communiqué trouvé</p>";
            }
        ?>

        <?php 
        require_once("footer.php");
         ?>
</body>
</html>
