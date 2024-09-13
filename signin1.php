<?php
    include "./ConnexionDb.php";
    $con=connexionDb($HostName,$UserName,$Passeword,$DataBase);

    if(isset($_POST)){
        $nom=$_POST["nom"];
        $prenom=$_POST["prenom"];
        $email=$_POST['email'];
        $password=$_POST["password"];
        $niveau=$_POST["niveau"];
        echo "$nom , $prenom , $email , $hashed_password , $niveau";
        if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($password) && !empty($niveau)) {
            // Hachage du mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
            // Préparation de la requête SQL
            $stmt = $con->prepare("INSERT INTO user (nom,prenom, email,password,niveau) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss',$nom,$prenom, $email, $hashed_password,$niveau);
        
            // Exécution de la requête
            if ($stmt->execute()) {
                echo "Inscription réussie !";
            } else {
                echo "Erreur lors de l'inscription : " . $stmt->error;
            }
        
            // Fermeture de la requête
            $stmt->close();
        } else {
            echo "Veuillez remplir tous les champs.";
        }
    }
