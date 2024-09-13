<?php
session_start(); // Démarrer la session pour accéder aux données de session

header('Content-Type: application/json');

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "maxime";
$dbname = "archiva";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion: ' . $conn->connect_error]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$cerId = $data['cerId'];

// Fonction pour obtenir les IDs des CERs likés à partir des cookies
function getLikedCersFromCookies() {
    if (isset($_COOKIE['liked_cers'])) {
        return json_decode($_COOKIE['liked_cers'], true);
    }
    return [];
}

// Fonction pour mettre à jour les cookies avec les IDs des CERs likés
function updateLikedCersCookie($likedCers) {
    setcookie('liked_cers', json_encode($likedCers), time() + (86400 * 30), "/"); // Cookie valide 30 jours
}

// Vérifier si le CER a déjà été liké par l'utilisateur
$likedCersFromSession = isset($_SESSION['liked_cers']) ? $_SESSION['liked_cers'] : [];
$likedCersFromCookies = getLikedCersFromCookies();
$allLikedCers = array_unique(array_merge($likedCersFromSession, $likedCersFromCookies));
$hasLiked = in_array($cerId, $allLikedCers);

if ($hasLiked) {
    echo json_encode(['success' => false, 'message' => 'Vous avez déjà liké ce CER']);
    $conn->close();
    exit();
}

// Exemple simple : augmentation du compteur de likes
$sql = "UPDATE cer SET likes = likes + 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $cerId);

if ($stmt->execute()) {
    // Enregistrez le CER dans les cookies et la session
    $_SESSION['liked_cers'][] = $cerId;
    $allLikedCers[] = $cerId;
    updateLikedCersCookie($allLikedCers);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour des likes']);
}

$stmt->close();
$conn->close();
?>
