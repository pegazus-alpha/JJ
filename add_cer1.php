<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "maxime";
$dbname = "archiva";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Requête pour obtenir tous les CERs, leur spécialité et leur niveau
$sql = "SELECT cer.id, cer.titre, domaine.nom AS domaine, niveau.nom AS niveau
        FROM cer
        JOIN tags ON cer.id = tags.cer
        JOIN domaine ON tags.domaine = domaine.id
        JOIN niveau ON cer.niveau = niveau.id";
$result = $conn->query($sql);
?>

<?php include "header.php"; ?>

<main>
    <div class="title">
        <h2>Tous les CERs</h2>
    </div>

    <div class="container">
        <div class="header">
            <button class="add-cer-btn" id="addCerBtn">Ajouter un CER</button>
        </div>
    
        <div class="filter">
            <label>Filtrer: <a href="#">Tous</a></label>
        </div>

        <table class="cer-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Spécialité</th>
                    <th>Niveaux</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Affichage des CERs
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['titre']) . "</td>
                                <td>" . htmlspecialchars($row['domaine']) . "</td>
                                <td>" . htmlspecialchars($row['niveau']) . "</td>
                                <td>" . "</td>
                                <td>
                                    <button class='edit-btn' onclick=\"location.href='edit_cer.php?id=" . $row['id'] . "'\">Éditer</button>
                                    <button class='delete-btn' onclick=\"deleteCer(" . $row['id'] . ")\">Supprimer</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Aucun CER trouvé</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div id="cerModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Ajouter un nouveau CER</h2>
            
           <?php $niveau_sql = "SELECT * FROM niveau";
$niveau_result = $conn->query($niveau_sql);

// Requête pour obtenir les domaines de spécialité
$specialite_sql = "SELECT * FROM domaine";
$specialite_result = $conn->query($specialite_sql);
?>

<form action="add_cer.php" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="titre">Titre du CER</label>
        <input type="text" id="titre" name="titre" placeholder="Exemple: Prosit 4.3 - Recherche Opérationnelle" required>
    </div>

    <div class="form-group">
        <label for="niveau">Niveaux</label>
        <select id="niveaux" name="niveau" required>
            <?php if ($niveau_result->num_rows > 0): ?>
                <?php while ($row = $niveau_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>">
                        <?php echo htmlspecialchars($row['nom']); ?>
                    </option>
                <?php endwhile; ?>
            <?php else: ?>
                <option value="">Aucun niveau disponible</option>
            <?php endif; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="specialite[]">Domaines de spécialité</label>
        <?php if ($specialite_result->num_rows > 0): ?>
            <?php while ($row = $specialite_result->fetch_assoc()): ?>
                <div>
                    <input type="checkbox" id="specialite_<?php echo $row['id']; ?>" name="specialite[]" value="<?php echo $row['id']; ?>">
                    <label for="specialite_<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['nom']); ?>
                    </label>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Aucun domaine de spécialité disponible</p>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="description">Description du CER</label>
        <textarea id="description" name="description" placeholder="Entrez ici la description ou un résumé de votre CER"></textarea>
    </div>

    <div class="form-group">
        <label for="fichier">Fichiers du CER</label>
        <input type="file" id="fichier" name="fichier">
        <p>Cliquez ici pour attacher le fichier du CER (.pdf ou .docx)</p>
    </div>

    <div class="form-group">
        <label for="image">Image du CER</label>
        <input type="file" id="image" name="image" accept="image/*">
        <p>Cliquez ici pour ajouter une image associée au CER (.png, .jpg, .jpeg)</p>
    </div>

    <div class="form-group">
        <button type="submit" class="btn-enregistrer">Enregistrer</button>
    </div>
</form>
        </div>
    </div>
</main>

<footer>
    <!-- Contenu du pied de page -->
    <div class="footer-container">
        <div class="footer-logo-contact">
            <img src="assets/images/logo.png" alt="Archiva Logo" class="footer-logo">
            <p class="footer-contact">
                <i class="fas fa-envelope"></i> info@archiva.com<br>
                <i class="fas fa-phone"></i> +237 600 000 000<br>
                <i class="fas fa-map-marker-alt"></i> Yassa, Douala/Cameroun
            </p>
        </div>
        <div class="footer-links">
            <h3>Accueil</h3>
            <ul>
                <li><a href="#">CERs</a></li>
                <li><a href="#">Mes CERs favoris</a></li>
                <li><a href="#">Gestion de CER</a></li>
            </ul>
        </div>
        <div class="footer-social">
            <h3>Social Profiles</h3>
            <ul>
                <li><a href="#"><i class="fa-brands fa-square-facebook"></i></a></li>
                <li><a href="#"><i class="fab fa-twitter"></i> </a></li>
                <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2024 Archiva. Tous droits réservés.</p>
    </div>
</footer>

<script>
// Script pour ouvrir et fermer le modal
var modal = document.getElementById("cerModal");
var btn = document.getElementById("addCerBtn");
var span = document.getElementsByClassName("close")[0];

btn.onclick = function() {
    modal.style.display = "block";
}

span.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function deleteCer(id) {
    if (confirm("Voulez-vous vraiment supprimer ce CER?")) {
        window.location.href = "delete_cer.php?id=" + id;
    }
}
</script>

</body>
</html>

<?php
$conn->close();
?>
