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

// Vérifier si les données du formulaire sont soumises
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hachage du mot de passe
    $niveau = $_POST['niveau'];

    // Insérer les données dans la base de données
    $sql = "INSERT INTO utilisateurs (nom, prenom, email, password, niveau_id) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nom, $prenom, $email, $password, $niveau);

    if ($stmt->execute()) {
        // Récupérer l'ID de l'utilisateur inséré
        $user_id = $conn->insert_id;

        // Si l'inscription réussit, enregistrer les informations de l'utilisateur en session
        $_SESSION['user'] = [
            'id' => $user_id,        // ID de l'utilisateur
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'niveau' => $niveau
        ];

        // Redirection vers la page d'accueil ou tableau de bord
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Erreur: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
?>
