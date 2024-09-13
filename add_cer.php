<?php
// Démarrer la session
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

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer l'ID de l'utilisateur via la session
    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['id'];
    } else {
        die("Utilisateur non connecté.");
    }

    $titre = $_POST['titre'];
    $niveau = $_POST['niveau'];
    $description = $_POST['description'];
    $specialites = isset($_POST['specialite']) ? $_POST['specialite'] : [];
    $fichier = $_FILES['fichier'];
    $image = $_FILES['image']; // Nouveau champ d'image

    // Chemin pour stocker l'image avec un nom unique
    $imagePath = '';
    if (isset($image) && $image['error'] == 0) {
        $target_dir = "uploads/images/";
        $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
        $uniqueImageName = uniqid() . '.' . $imageFileType; // Générer un nom unique pour l'image
        $imagePath =$uniqueImageName;

        // Vérifier si l'image est un fichier valide
        $check = getimagesize($image["tmp_name"]);
        if ($check !== false) {
            // Vérifier les extensions autorisées
            if (in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                if (move_uploaded_file($image["tmp_name"], $imagePath)) {
                    echo "L'image " . $uniqueImageName . " a été téléchargée.";
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

    // Chemin pour stocker le fichier CER avec un nom unique
    $filePath = '';
    if (isset($fichier) && $fichier['error'] == 0) {
        $target_dir = "uploads/";
        $fileType = strtolower(pathinfo($fichier["name"], PATHINFO_EXTENSION));
        $uniqueFileName = uniqid() . '.' . $fileType; // Générer un nom unique pour le fichier
        $filePath =$uniqueFileName;

        // Vérifier les types de fichiers autorisés
        if (in_array($fileType, ['pdf', 'docx'])) {
            if (move_uploaded_file($fichier["tmp_name"], $filePath)) {
                echo "Le fichier " . $uniqueFileName . " a été téléchargé.";
            } else {
                echo "Erreur lors du téléchargement du fichier.";
            }
        } else {
            echo "Seuls les fichiers PDF et DOCX sont autorisés.";
        }
    }

    // Préparer et exécuter la requête d'insertion dans la table 'cer'
    $stmt = $conn->prepare("INSERT INTO cer (titre, niveau, user, description, image, fichier) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siisss", $titre, $niveau, $user_id, $description, $imagePath, $filePath);

    if ($stmt->execute()) {
        $cer_id = $stmt->insert_id;

        // Insérer les spécialités dans la table 'tags'
        foreach ($specialites as $specialite) {
            $stmt = $conn->prepare("INSERT INTO tags (cer, domaine) VALUES (?, ?)");
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
