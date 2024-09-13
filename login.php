<?php

// Démarrer la session
session_start();

// Inclure la connexion à la base de données
include "./ConnexionDb.php";
$conn=connexionDb($HostName, $UserName, $Passeword, $DataBase);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données POST
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Vérification des informations de connexion
    if (!empty($email) && !empty($password)) {
        // Préparation de la requête SQL pour récupérer l'utilisateur correspondant à l'email
        $stmt = $conn->prepare('SELECT * FROM user WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Vérification du mot de passe haché
        if ($user && password_verify($password, $user['password'])) {
            // Enregistrement des informations utilisateur en session
            $_SESSION['user'] = [
                'id' => $user['id'],        // ID de l'utilisateur
                'nom' => $user['nom'],      // Nom de l'utilisateur
                'prenom' => $user['prenom'],// Prénom de l'utilisateur
                'email' => $user['email'],  // Email de l'utilisateur
                'niveau' => $user['niveau'] // Niveau de l'utilisateur
                // Ajoutez d'autres informations que vous souhaitez conserver en session
            ];

            // Redirection vers le tableau de bord ou une autre page après connexion réussie
            header("Location: add_cer1.php");
            exit();
        } else {
            echo "Email ou mot de passe incorrect.";
        }

        // Fermeture de la requête
        $stmt->close();
    } else {
        echo "Veuillez fournir un email et un mot de passe.";
    }
}
?>
