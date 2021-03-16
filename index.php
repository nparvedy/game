<?php 

    // objectif combat : pendant que le groupe combat, il y aura un temps de combat, le groupe revient qu'à la fin du combat.
    // le temps du combat est déterminé par les compétences des héros ou des monstres combatut
    session_start();
    require 'config/bdd.php';
    require 'config/UserConfig.php';
    require 'game/User.php';

    $bdd = new BDD;
    
    if (is_string($bdd)){
        return $bdd->getPDO();
    }else {
        if (isset($_POST['pseudo']) && isset($_POST['email']) && isset($_POST['password'])){
            if (!empty($_POST['pseudo']) && !empty($_POST['email']) && !empty($_POST['password'])){
                if ($bdd->prepareAccount($_POST['email']) == true){
                    echo "Email déjà pris";
                }else {
                    $bdd->addAccount($_POST['pseudo'], $_POST['email'], $_POST['password']);
                    echo "Utilisateur ajouté";
                }
            }else {
                echo "ça manque d'infos";
            }
        }

        if (isset($_POST['emailC']) && isset($_POST['passwordC'])){
            if (!empty($_POST['emailC']) && !empty($_POST['passwordC'])){
                $value = $bdd->prepareAccount($_POST['emailC'], "connexion");
                if (is_string($value)){
                    echo $value;
                }else if (is_array($value)){
                    if ($_POST['passwordC'] === $value[0]['password']){
                        $_SESSION['pseudo'] = $value[0]["pseudo"];
                        $_SESSION['email'] = $value[0]['email'];
                        $_SESSION['id'] = $value[0]['id'];
                        header('Location:' . $_SERVER["REQUEST_URI"]); // permet de recharger la page sans garder les infos (req URI)
                    }
                }
            }else {
                echo "ça manque d'infos";
            }
        }
    }
    

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Un jeu</title>
</head>

<body>
    <?php
    require "game/game.php";
    //$game = new Game;
    ?>

    <?php 
    //On récupère l'e-mail de la session pour créer notre objet User
        if (isset($_SESSION['pseudo'])){
            $userConfig = new UserConfig($bdd);
            $dataUser = $userConfig->getDataUser($_SESSION['email']);
            $user = new User($dataUser[0]['id'], $dataUser[0]['pseudo'], $dataUser[0]['email']);
            ?>
        <h2>Bienvenu <?= $_SESSION['pseudo']; ?></h2>

            
    
    <ul>
        <li><a href="pages/recrutement.php">Taverne</a></li>
        <li><a href="pages/aventure.php">Aventure</a></li>
        <li><a href="pages/deconnexion.php">Déconnexion</a></li>
    </ul>
            <?php
        }else {
    ?>

    <h2>Inscription</h2>

    <form action="" method="post" class="form-suscribe">
        <label for="pseudo">Entrez votre pseudo </label>
        <input type="text" name="pseudo" id="name" required>
        <label for="email">Entrez votre adresse mail </label>
        <input type="email" name="email" id="email" required>
        <label for="password">Entrez votre mot de passe </label>
        <input type="password" name="password" id="password" required>
        <input type="submit" value="Inscription">
    </form>

    <h2>Connexion</h2>

    <form action="" method="post" class="form-connexion">
        <label for="email">Entrez votre adresse mail </label>
        <input type="email" name="emailC" id="email" required>
        <label for="password">Entrez votre mot de passe </label>
        <input type="password" name="passwordC" id="password" required>
        <input type="submit" value="Connexion">
    </form>
    <?php
        }
    ?>

</body>
</html>