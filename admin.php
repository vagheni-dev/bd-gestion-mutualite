<div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>matricule</th>
                    <th>Nom</th>
                    <th>postnom</th>
                    <th>contact</th>
                    <th>genre</th>
                    <th>fonction</th>
                    <th>Adresse</th>
                    <th>mail</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($result as $row) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['matricule']); ?></td>
                    <td><?php echo htmlspecialchars($row['nom']); ?></td>
                    <td><?php echo htmlspecialchars($row['postnom']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                    <td><?php echo htmlspecialchars($row['genre']); ?></td>
                    <td><?php echo htmlspecialchars($row['fonction']); ?></td>
                    <td><?php echo htmlspecialchars($row['adresse']); ?></td>
                    <td><?php echo htmlspecialchars($row['mail']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
