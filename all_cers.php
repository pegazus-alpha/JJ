<?php 
include "header.php"; 

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "maxime";
$dbname = "archiva";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Pagination
$limit = 6; // Nombre d'éléments par page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Rechercher et trier
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $conn->real_escape_string($_GET['sort']) : 'nom';

// Requête pour obtenir le nombre total d'éléments
$total_sql = "SELECT COUNT(*) as total FROM cer WHERE titre LIKE '%$search%'";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];
$total_pages = ceil($total_items / $limit);

// Requête pour obtenir les CERs avec pagination, en joignant la table user pour obtenir les noms et prénoms
$sql = "SELECT cer.id, cer.titre AS titre, cer.description, cer.user,cer.image As _image, domaine.nom AS domaine, cer.niveau,
               cer.fichier AS fichier,user.nom AS author_nom, user.prenom AS author_prenom
        FROM cer
        JOIN tags ON cer.id = tags.cer
        JOIN domaine ON tags.domaine = domaine.id
        JOIN user ON cer.user = user.id
        WHERE cer.titre LIKE '%$search%'
        ORDER BY " . ($sort == 'nom' ? 'cer.titre' : ($sort == 'auteur' ? 'user.nom' : 'cer.date')) . "
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Requêtes pour obtenir les options des filtres
$niveau_sql = "SELECT DISTINCT niveau FROM cer";
$niveau_result = $conn->query($niveau_sql);

$domaine_sql = "SELECT * FROM domaine";
$domaine_result = $conn->query($domaine_sql);
?>

<main>
    <div class="title">
        <h2>Tous les CERs</h2>
    </div>

    <div class="search-bloc">
        <div class="cer-numbers">
            <p><?php echo htmlspecialchars($total_items); ?> CERs Au total</p>
        </div>

        <div class="search">
            <form action="" method="get">
                <input type="text" name="search" placeholder="Rechercher un CER" value="<?php echo htmlspecialchars($search); ?>" />
                <input type="hidden" name="page" value="<?php echo $page; ?>" />
                <i class="fa fa-search"></i>
            </form>
        </div>

        <div class="sort-bloc">
            <label for="sort">Trier par : </label>
            <form action="" method="get">
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="nom" <?php echo $sort == 'nom' ? 'selected' : ''; ?>>Nom</option>
                    <option value="auteur" <?php echo $sort == 'auteur' ? 'selected' : ''; ?>>Auteur</option>
                    <option value="date" <?php echo $sort == 'date' ? 'selected' : ''; ?>>Date</option>
                </select>
                <input type="hidden" name="page" value="<?php echo $page; ?>" />
            </form>
        </div>
    </div>

    <section class="all-cer-list">
        <div class="cer-filter">
            <div class="level-filter">
                <h3>Niveaux</h3>
                <form action="" method="get">
                    <select name="niveau" onchange="this.form.submit()">
                        <option value="">Tous les niveaux</option>
                        <?php if ($niveau_result->num_rows > 0): ?>
                            <?php while ($row = $niveau_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($row['niveau']); ?>" 
                                    <?php echo isset($_GET['niveau']) && $_GET['niveau'] == $row['niveau'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['niveau']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                    <input type="hidden" name="page" value="<?php echo $page; ?>" />
                </form>
            </div>

            <div class="spec-filter">
                <h3>Domaines de spécialité</h3>
                <form action="" method="get">
                    <?php if ($domaine_result->num_rows > 0): ?>
                        <?php while ($row = $domaine_result->fetch_assoc()): ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="domaine[]" value="<?php echo htmlspecialchars($row['id']); ?>" 
                                        <?php if (isset($_GET['domaine']) && in_array($row['id'], $_GET['domaine'])) echo 'checked'; ?>>
                                    <?php echo htmlspecialchars($row['nom']); ?>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    <input type="hidden" name="page" value="<?php echo $page; ?>" />
                    <button type="submit">Filtrer</button>
                </form>
            </div>
        </div>

        <div class="cer-cards">
            <div class="cer-cards-row">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="cer-card">
                            <div class="cer-card__image"><img src="uploads/images/<?php echo $row['_image'] ?>" alt="Image"></div>
                            <div class="cer-info">
                            <div class="author">
                            <?php echo $row['id']; ?>
                                <i class="fa-regular fa-heart" data-id="<?php echo $row['id']; ?>" onclick="likeCer(this)"></i>
                                <p>par <?php echo htmlspecialchars($row['author_nom'] . ' ' . $row['author_prenom']); ?></p>
                            </div>
                                <h3><?php echo htmlspecialchars($row['titre']); ?></h3>
                                <p><?php echo htmlspecialchars($row['description']); ?></p>
                                <!-- <button class="consult">Consulter le CER</button> -->
                                 <!-- Bouton pour consulter le fichier associé -->
                                <?php if (!empty($row['fichier'])): ?>
                                    <a href="uploads/<?php echo htmlspecialchars($row['fichier']); ?>" target="_blank">
                                        <button class="consult">Consulter le CER</button>
                                    </a>
                                <?php else: ?>
                                    <button class="consult" disabled>Aucun fichier disponible</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Aucun CER trouvé</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>" class="page">Précédent</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>" class="page <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>" class="page">Suivant</a>
        <?php endif; ?>
    </div>
</main>
<script>
    function likeCer(element) {
    const cerId = element.getAttribute('data-id');
    
    fetch('like.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ cerId: cerId })
    })
    .then(response => response.text()) // Lire la réponse en texte
    .then(text => {
        try {
            const data = JSON.parse(text); // Tenter de parser en JSON
            if (data.success) {
                // Mise à jour de l'interface utilisateur en cas de succès
                element.classList.add('liked');
                alert('CER liké avec succès !');
            } else {
                // Affichage d'un message d'erreur en cas d'échec
                alert('Erreur: ' + data.message);
            }
        } catch (e) {
            console.error('Erreur de parsing JSON:', e);
            alert('Erreur lors de la réception des données.');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

</script>

<?php
$conn->close();
?>
