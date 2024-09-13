<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditer un CER</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <h1>Éditer le CER</h1>

    <?php
    // Connexion à la base de données
    $db = new mysqli('localhost', 'root', 'maxime', 'archiva');
    
    // Vérifier la connexion
    if ($db->connect_error) {
        die("Erreur de connexion : " . $db->connect_error);
    }

    // Récupérer les détails du CER à modifier
    $cer_id = $_GET['id'];
    $query = $db->prepare("SELECT * FROM cer WHERE id = ?");
    $query->bind_param('i', $cer_id);
    $query->execute();
    $result = $query->get_result();
    $cer = $result->fetch_assoc();

    // Récupérer les niveaux depuis la table 'niveau'
    $niveaux_query = $db->query("SELECT * FROM niveau");
    $niveaux = $niveaux_query->fetch_all(MYSQLI_ASSOC);

    // Récupérer les spécialités depuis la table 'domaine'
    $specialites_query = $db->query("SELECT * FROM domaine");
    $specialites_possibles = $specialites_query->fetch_all(MYSQLI_ASSOC);

    // Récupérer les spécialités actuelles du CER
    $specialites_actuelles_query = $db->prepare("SELECT domaine.nom FROM tags JOIN domaine ON tags.domaine = domaine.id WHERE tags.cer = ?");
    $specialites_actuelles_query->bind_param('i', $cer_id);
    $specialites_actuelles_query->execute();
    $result_specialites = $specialites_actuelles_query->get_result();
    $specialites_actuelles = [];
    while ($row = $result_specialites->fetch_assoc()) {
        $specialites_actuelles[] = $row['nom'];
    }
    ?>

    <form action="cer_edit.php?id=<?php echo $cer_id; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="titre">Titre du CER</label>
            <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($cer['titre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="niveau">Niveaux</label>
            <select id="niveau" name="niveau">
                <?php
                foreach ($niveaux as $niveau) {
                    $selected = ($cer['niveau'] == $niveau['nom']) ? 'selected' : '';
                    echo "<option value=\"{$niveau['id']}\" $selected>{$niveau['nom']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="specialite[]">Domaines de spécialité</label>
            <?php
            foreach ($specialites_possibles as $specialite) {
                $checked = in_array($specialite['nom'], $specialites_actuelles) ? 'checked' : '';
                echo "<div>
                        <input type='checkbox' id='{$specialite['nom']}' name='specialite[]' value='{$specialite['id']}' $checked>
                        <label for='{$specialite['nom']}'>{$specialite['nom']}</label>
                      </div>";
            }
            ?>
        </div>

        <div class="form-group">
            <label for="description">Description du CER</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($cer['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="fichier">Fichier du CER (laisser vide pour conserver l'ancien fichier)</label>
            <input type="file" id="fichier" name="fichier">
            <p>Fichier actuel : <?php echo htmlspecialchars($cer['fichier']); ?></p>
        </div>

        <div class="form-group">
            <button type="submit" class="btn-enregistrer">Enregistrer les modifications</button>
        </div>
    </form>

</body>
</html>
