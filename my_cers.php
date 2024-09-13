<?php
include "header.php";

// Démarrer la session pour accéder aux données de session
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "maxime";
$dbname = "archiva";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Récupérer les IDs des CERs likés depuis la session et les cookies
$liked_cers = isset($_SESSION['liked_cers']) ? $_SESSION['liked_cers'] : [];
if (isset($_COOKIE['liked_cers'])) {
    $cookie_liked_cers = json_decode($_COOKIE['liked_cers'], true);
    $liked_cers = array_merge($liked_cers, $cookie_liked_cers);
}
$liked_cers = array_unique($liked_cers); // Éviter les doublons

// Si aucun CER n'a été liké, afficher un message approprié
if (empty($liked_cers)) {
    echo "<p>Aucun CER n'a été liké.</p>";
} else {
    // Préparer la requête pour obtenir les détails des CERs likés
    $placeholders = implode(',', array_fill(0, count($liked_cers), '?'));
    $sql = "SELECT * FROM cer WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    // Lier les paramètres
    $types = str_repeat('i', count($liked_cers));
    $stmt->bind_param($types, ...$liked_cers);
    
    $stmt->execute();
    $result = $stmt->get_result();
    ?>
    
    <main>
        <div class="title">
            <h2>Tous les CERs Likés</h2>
        </div>

        <div class="container">
            <div class="header">
                <button class="add-cer-btn">Ajouter un CER</button>
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
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['titre']); ?></td>
                            <td><?php echo htmlspecialchars($row['domaine']); ?></td>
                            <td><?php echo htmlspecialchars($row['niveau']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td>
                                <button class="edit-btn">Éditer</button>
                                <button class="delete-btn">Supprimer</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php
    $stmt->close();
}

$conn->close();
?>
<footer>
    <!-- Footer content -->
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
                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2024 Archiva. All rights reserved.</p>
    </div>
</footer>
</body>
</html>
