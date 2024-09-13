<?php include "header.php"; ?>

<main>
    <section>
        <div class="search-container">
            <div class="search-box">
                <i class="fa fa-search"></i>
                <input type="text" placeholder="Rechercher un CER" />
            </div>
        </div>            
    </section>

    <section class="hero">
        <div class="welcome-bloc">
            <img src="assets/images/accent.png" alt="Icône d'accentuation" class="accent-icon">
            <h1>Bienvenue sur Archiva, votre espace</h1>
        </div>
        <h2>Espace d'archivage d'anciens CERs</h2>
        <p>L'homme n'est rien sans son bord</p>
        <div class="hero-buttons">
            <button class="explorer">Explorer plus de CER</button>
            <button class="all-cer">Tous les CERs</button>
        </div>
    </section>

    <section class="cer-list">
        <div class="titre-liste">
            <div class="desc-liste">
                <h2>Les meilleurs CERs du moment</h2>
                <p>Découvrez ci-dessous les CERs les plus appréciés par notre communauté d'utilisateurs. Ces CERs ont été sélectionnés et évalués par nos membres en fonction de leur qualité, pertinence et utilité.</p>
            </div>
            <button class="all-cer">Voir plus</button>
        </div>
        
        <div class="cer-cards">
            <?php
            // Connect to the database
            $conn = new mysqli('localhost', 'root', 'maxime', 'archiva');

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch top 6 CERs by likes
            $sql = "SELECT * FROM cer ORDER BY likes DESC LIMIT 6";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    $title = $row["titre"];
                    // $image = $row["image"];
                    $author = $row["user"];
                    $description = $row["description"];
                    ?>
                    <div class="cer-card">
                        <div class="cer-card__image"><img src="assets/images/<?php echo ""; ?>" alt="<?php echo $title; ?>"></div>
                        <div class="cer-info">
                            <div class="author">
                                <i class="fa-regular fa-heart"></i>
                                <p>par <?php echo $author; ?></p>
                            </div>
                            <h3><?php echo $title; ?></h3>
                            <p><?php echo $description; ?></p>
                            <button class="consult">Consulter le CER</button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "Aucun CER trouvé.";
            }

            $conn->close();
            ?>
        </div>
    </section>
</main>

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
                <li><a href="#"><i class="fab fa-twitter"></i> </a></li>
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
