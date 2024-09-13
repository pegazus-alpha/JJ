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

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $niveau = $_POST['niveau'];
    $description = $_POST['description'];
    $specialites = isset($_POST['specialite']) ? $_POST['specialite'] : [];
    $fichier = $_FILES['fichier'];
    $image = $_FILES['image']; // New image field

    // Chemin pour stocker l'image
    $imagePath = '';
    if (isset($image) && $image['error'] == 0) {
        $target_dir = "uploads/images/";
        $imagePath = $target_dir . basename($image["name"]);
        $imageFileType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        // Vérifier si l'image est un fichier valide
        $check = getimagesize($image["tmp_name"]);
        if ($check !== false) {
            // Vérifier les extensions autorisées
            if (in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                if (move_uploaded_file($image["tmp_name"], $imagePath)) {
                    echo "L'image " . basename($image["name"]) . " a été téléchargée.";
                } else {
                    echo "Erreur lors du téléchargement de l'image.";
                }
            } else {
                echo "Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
            }
        } else {
            echo "Le fichier téléchargé n'est pas une image.";
        }
    }

    // Préparer et exécuter la requête d'insertion
    $stmt = $conn->prepare("INSERT INTO cer (titre, niveau, description, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $titre, $niveau, $description, $imagePath);

    if ($stmt->execute()) {
        $cer_id = $stmt->insert_id;

        // Insérer les spécialités dans la table tags
        foreach ($specialites as $specialite) {
            $stmt = $conn->prepare("INSERT INTO tags (cer_id, specialite_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $cer_id, $specialite);
            $stmt->execute();
        }

        echo "Le CER a été ajouté avec succès.";
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
