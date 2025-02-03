<?php 
$q = htmlspecialchars($_GET['q']);
require_once("connexion/connexion.php");

$sql = $pdo->prepare("SELECT * FROM `admin` WHERE nom LIKE ? OR email LIKE ?");
$sql->execute(array($q.'%', $q.'%'));
$result = $sql->fetchAll(PDO::FETCH_ASSOC);

if (count($result) > 0) { ?>

<div class="container mt-4">
  <table class="table table-bordered">
    <thead class="thead-dark">
      <tr>
        <th>id</th>
        <th>nom</th>
        <th>email</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($result as $row) {
        $id = $row['id'];
        $nom = $row['nom'];
        $email = $row['email'];
      ?>
      <tr>
        <td><?php echo htmlspecialchars($id); ?></td>
        <td><?php echo htmlspecialchars($nom); ?></td>
        <td><?php echo htmlspecialchars($email); ?></td>
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
