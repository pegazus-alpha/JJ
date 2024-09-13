<?php
// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$pass = 'maxime';
$db_name = 'archiva';

$conn = new mysqli($host, $user, $pass, $db_name);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Récupérer l'ID du CER à éditer
$cer_id = $_GET['id'];

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $titre = $_POST['titre'];
    $niveau = $_POST['niveau'];
    $specialites = isset($_POST['specialite']) ? $_POST['specialite'] : array(); // Gérer le tableau de spécialités
    $specialites_str = implode(", ", $specialites); // Fusionner les spécialités en une chaîne
    $description = $_POST['description'];
    
    // Gestion du fichier uploadé (si un nouveau fichier est téléchargé)
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] == 0) {
        $file_name = $_FILES['fichier']['name'];
        $file_tmp = $_FILES['fichier']['tmp_name'];
        $file_size = $_FILES['fichier']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = array('pdf', 'docx');

        if (in_array($file_ext, $allowed)) {
            if ($file_size <= 5000000) {
                $file_new_name = uniqid('', true) . '.' . $file_ext;
                $file_dest = 'uploads/' . $file_new_name;

                if (move_uploaded_file($file_tmp, $file_dest)) {
                    // Supprimer l'ancien fichier si un nouveau fichier a été uploadé
                    if (!empty($cer['fichier']) && file_exists('uploads/' . $cer['fichier'])) {
                        unlink('uploads/' . $cer['fichier']);
                    }
                    $fichier = $file_new_name;
                } else {
                    echo "Erreur lors de l'upload du fichier.";
                    exit;
                }
            } else {
                echo "Fichier trop volumineux. Taille maximale : 5 Mo.";
                exit;
            }
        } else {
            echo "Type de fichier non autorisé. Seuls les fichiers PDF ou DOCX sont acceptés.";
            exit;
        }
    } else {
        // Si aucun nouveau fichier n'est uploadé, garder l'ancien fichier
        $fichier = $cer['fichier'];
    }

    // Mise à jour des informations dans la base de données
    $sql = "UPDATE cer SET titre = ?, niveau = ?, domaine = ?, description = ?, fichier = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $titre, $niveau, $specialites_str, $description, $fichier, $cer_id);

    if ($stmt->execute()) {
        echo "CER mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour du CER : " . $stmt->error;
    }

    // Mise à jour des tags dans la table 'tags' (supprimer les anciens et ajouter les nouveaux)
    $sql_delete_tags = "DELETE FROM tags WHERE cer_id = ?";
    $stmt = $conn->prepare($sql_delete_tags);
    $stmt->bind_param("i", $cer_id);
    $stmt->execute();

    foreach ($specialites as $specialite) {
        $sql_insert_tag = "INSERT INTO tags (cer_id, domaine) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_insert_tag);
        $stmt->bind_param("is", $cer_id, $specialite);
        $stmt->execute();
    }

    // Fermer la connexion
    $stmt->close();
    $conn->close();

    // Rediriger vers une autre page ou afficher un message de succès
    header("Location: success.php");
    exit;
}

// Récupérer les données actuelles du CER à partir de la base de données
$sql = "SELECT * FROM cer WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cer_id);
$stmt->execute();
$result = $stmt->get_result();
$cer = $result->fetch_assoc();

// Si le CER n'existe pas, afficher un message d'erreur
if (!$cer) {
    echo "CER non trouvé.";
    exit;
}
?>
