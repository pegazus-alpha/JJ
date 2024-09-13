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

    <form action="edit_cer.php?id=<?php echo $cer_id; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="titre">Titre du CER</label>
            <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($cer['titre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="niveau">Niveaux</label>
            <select id="niveau" name="niveau">
                <?php
                $niveaux = ['X1', 'X2', 'X3', 'X4', 'X5'];
                foreach ($niveaux as $niveau) {
                    $selected = ($cer['niveau'] == $niveau) ? 'selected' : '';
                    echo "<option value=\"$niveau\" $selected>$niveau</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="specialite[]">Domaines de spécialité</label>
            <?php
            $specialites_possibles = ['Gestion de projet', 'Génie-logiciel', 'Réseaux & Infra', 'Sécurité', 'Data'];
            $specialites_actuelles = explode(', ', $cer['domaine']); // On part du principe que les domaines sont stockés en tant que chaîne de caractères

            foreach ($specialites_possibles as $specialite) {
                $checked = in_array($specialite, $specialites_actuelles) ? 'checked' : '';
                echo "<div>
                        <input type='checkbox' id='$specialite' name='specialite[]' value='$specialite' $checked>
                        <label for='$specialite'>$specialite</label>
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
