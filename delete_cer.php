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

// Vérifier si l'ID du CER est passé en paramètre
if (isset($_GET['id'])) {
    $cer_id = intval($_GET['id']);

    // Démarrer une transaction
    $conn->begin_transaction();

    try {
        // Supprimer les entrées de la table tags liées au CER
        $sql1 = "DELETE FROM tags WHERE cer = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("i", $cer_id);
        $stmt1->execute();

        // Supprimer le CER de la table cer
        $sql2 = "DELETE FROM cer WHERE id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $cer_id);
        $stmt2->execute();

        // Commit la transaction
        $conn->commit();

        // Redirection vers la page de liste des CERs avec un message de succès
        header("Location: all_cers.html?message=CER supprimé avec succès");
        exit();
    } catch (Exception $e) {
        // En cas d'erreur, rollback la transaction
        $conn->rollback();
        
        // Redirection vers la page de liste des CERs avec un message d'erreur
        header("Location: all_cers.php?error=Erreur lors de la suppression du CER");
        exit();
    }
} else {
    // Redirection si aucun ID n'est fourni
    header("Location: all_cers.php?error=Aucun ID fourni pour la suppression");
    exit();
}

$conn->close();

