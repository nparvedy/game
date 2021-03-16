<?php
session_start();
require '../config/bdd.php';
require '../config/UserConfig.php';
require '../game/User.php';
require '../game/Character.php';

$bdd = new BDD;

if (isset($_SESSION['pseudo'])){
    $userConfig = new UserConfig($bdd);
    $dataUser = $userConfig->getDataUser($_SESSION['email']);
    $user = new User($dataUser[0]['id'], $dataUser[0]['pseudo'], $dataUser[0]['email']);
//au changement d'une page, on a plus aucune information, doit-on créer une donnée session pour chaque information ?
}else {
    $newStr = str_replace('pages/recrutement.php', '', $_SERVER["REQUEST_URI"]);

    header('Location: ' . $newStr);
}

if (isset($_POST["class-character"]) && isset($_POST["char-name"])){
    $bdd->addCharacter($_SESSION['id'], $_POST["class-character"], $_POST["char-name"]);
    echo "ajout confirmé";

    header('Location: ' . $_SERVER["REQUEST_URI"]);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taverne</title>
</head>
<body>
    <h1>Taverne</h1>

    <ul>
        <li><a href="../index.php">Accueil</a></li>
        <li><a href="aventure.php">Aventure</a></li>
    </ul>

    <?php 
        $characterUser = $bdd->characterUser($_SESSION['id']);
        $nbCharacter = count($characterUser);
    ?>

    <p>Vous avez actuellement <?= $nbCharacter ?> joueurs sur 4 dans votre équipe.<br>Vous avez droit encore à <?= 4 - $nbCharacter ?> recrutement.</p>

    <?php 
        if (4 - $nbCharacter == 0){
            echo "<p>Désolé vous avez déjà une équipe au complet</p>";
        }else {
            
        
    ?>

    <form action="" method="post" class="add-character">
        <label for="class-select">Choisir une classe :</label>

        <label for="email">Donnez lui un nom</label>
        <input type="text" name="char-name" id="char-name" required>

        <select name="class-character" id="class-character">
            <option value="">--Choisissez une classe--</option>
            <option value="archer">Archer</option>
        </select>
        <input type="submit" value="Recruter">
    </form>
    <?php } ?>

    <?php 
        function classChar($characterUser, $bdd){
            for ($i = 0; $i < count($characterUser); $i++){
                $statsChar = $bdd->getStatsClass($characterUser[$i]['char_class'], $characterUser[$i]['char_level']);
                $character = new Character(
                    $characterUser[$i]['id'],
                    $characterUser[$i]['char_class'], 
                    $characterUser[$i]['char_name'], 
                    $characterUser[$i]['char_force'] + $statsChar[0]['char_force'], 
                    $characterUser[$i]['char_intelligence'] + $statsChar[0]['char_intelligence'], 
                    $characterUser[$i]['char_dexterity'] + $statsChar[0]['char_dexterity'], 
                    $characterUser[$i]['char_spirit'] + $statsChar[0]['char_spirit'],
                    $characterUser[$i]['char_life'] + $statsChar[0]['char_life'],
                    $characterUser[$i]['char_level'],
                    $characterUser[$i]['char_experience']
                );

                echo "<p>Vous avez recruté : </p>";
                echo "Name : " . $character->getCharName() . '<br>'; 
                echo "Classe : " . $character->getCharClass() . '<br>'; 
                echo "Force : " . $character->getCharForce() . '<br>'; 
                echo "Intelligence : " . $character->getCharIntelligence() . '<br>'; 
                echo "Dextérité : " . $character->getCharDexterity() . '<br>'; 
                echo "Esprit : " . $character->getCharSpirit() . '<br>'; 
                echo "Vie : " . $character->getCharLife() . '<br>'; 
                echo "Niveau : " . $character->getCharLevel() . '<br>'; 
                echo "Expérience : " . $character->getCharExperience() . '<br>'; 
            }
        }

        classChar($characterUser, $bdd);
    ?>
</body>
</html>