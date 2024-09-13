<?php 
/*
 * @Author: pegazus-alpha pourdebutantp@gmail.com
 * @Date: 2024-09-13 02:41:46
 * @LastEditors: pegazus-alpha pourdebutantp@gmail.com
 * @LastEditTime: 2024-09-13 20:59:20
 * @FilePath: \code\signin1.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

 

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "maxime";
$dbname = "archiva";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Requête pour récupérer les niveaux
$niveau_sql = "SELECT id, nom FROM niveau";
$niveau_result = $conn->query($niveau_sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archiva - Connexion</title>
    <link rel="stylesheet" href="styles/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-form">
                <h2>Se connecter</h2>
                <form action="signin.php" method="post">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Entrer votre nom" required>
                    
                    <label for="prenom">Prenom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Entrer votre prénom" required>
                    
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Entrer votre email" required>
                    
                    <label for="password">Mot de passe</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="Entrer votre mot de passe" required>
                        <span class="toggle-password">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="niveaux">Niveau</label>
                        <select class="sec" id="niveaux" name="niveau" required>
                            <option value="">Sélectionnez un niveau</option>
                            <?php 
                            // Génération dynamique des options de niveaux
                            if ($niveau_result->num_rows > 0) {
                                while($row = $niveau_result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
                                }
                            } else {
                                echo "<option value=''>Aucun niveau disponible</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Créer un Compte</button>
                    
                    <div class="register-link">
                        Vous avez déjà un compte?
                        <a href="login.html">Se connecter</a>
                    </div>
                </form>
            </div>
            <div class="login-image">
                <img src="assets/images/login.png" alt="Login Illustration">
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>