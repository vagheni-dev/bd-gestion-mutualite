<?php 
$q = htmlspecialchars($_GET['q']);
require_once("connexion/connexion.php");

$sql = $pdo->prepare("SELECT * FROM `user` WHERE nom LIKE ? OR mail LIKE ? OR postnom LIKE ?");
$sql->execute(array($q.'%', $q.'%', $q.'%'));
$result = $sql->fetchAll(PDO::FETCH_ASSOC);

if (count($result) > 0) { ?>

<div class="container mt-4">
  <table class="table table-bordered table-responsive-lg">
    <thead class="thead-dark">
      <tr>
        <th>matricule</th>
        <th>nom</th>
        <th>postnom</th>
        <th>mail</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($result as $row) {
        $matricule = $row['matricule'];
        $nom = $row['nom'];
        $postnom = $row['postnom'];
        $mail = $row['mail'];
      ?>
      <tr>
        <td><?php echo htmlspecialchars($matricule); ?></td>
        <td><?php echo htmlspecialchars($nom); ?></td>
        <td><?php echo htmlspecialchars($postnom); ?></td>
        <td><?php echo htmlspecialchars($mail); ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<?php } else { ?>
  <div class="container mt-4">
    <div class="alert alert-warning" role="alert">
      Pas de données trouvées.
    </div>
  </div>
<?php } ?>
