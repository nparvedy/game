<?php



date_default_timezone_set("Europe/Paris");
session_start();
require '../config/bdd.php';
require '../config/UserConfig.php';
require '../game/User.php';
require '../game/Character.php';
require '../game/Game.php';
require '../game/Monster.php';

$bdd = new BDD;

if (isset($_SESSION['pseudo'])) {
    $userConfig = new UserConfig($bdd);
    $dataUser = $userConfig->getDataUser($_SESSION['email']);
    $user = new User($dataUser[0]['id'], $dataUser[0]['pseudo'], $dataUser[0]['email']);
    //au changement d'une page, on a plus aucune information, doit-on créer une donnée session pour chaque information ?
} else {
    $newStr = str_replace('pages/aventure.php', '', $_SERVER["REQUEST_URI"]);

    header('Location: ' . $newStr);
}

if (isset($_POST["class-character"]) && isset($_POST["char-name"])) {
    $bdd->addCharacter(1, $_POST["class-character"], $_POST["char-name"]);
    echo "ajout confirmé";

    header('Location: ' . $_SERVER["REQUEST_URI"]);
}

$characterUser = $bdd->characterUser($_SESSION['id']);
$nbCharacter = count($characterUser);


function classChar($characterUser, $bdd)
{
    for ($i = 0; $i < count($characterUser); $i++) {
        $statsChar = $bdd->getStatsClass($characterUser[$i]['char_class'], $characterUser[$i]['char_level']);
        $nextLevel = $bdd->getNextLevel($characterUser[$i]['char_level']);
        $class = ucwords($characterUser[$i]['char_class']);

        spl_autoload_register(function ($class_name) {
            include '../game/classCharacter/' . $class_name . '.php';
        });
        
        $character[] = new $class(
            $characterUser[$i]['id'],
            $characterUser[$i]['char_class'],
            $characterUser[$i]['char_name'],
            $characterUser[$i]['char_force'] + $statsChar[0]['char_force'],
            $characterUser[$i]['char_intelligence'] + $statsChar[0]['char_intelligence'],
            $characterUser[$i]['char_dexterity'] + $statsChar[0]['char_dexterity'],
            $characterUser[$i]['char_spirit'] + $statsChar[0]['char_spirit'],
            $characterUser[$i]['char_life'] + $statsChar[0]['char_life'],
            $characterUser[$i]['char_level'],
            $characterUser[$i]['char_experience'] + 0,
            $nextLevel
        );
    }

    return $character;
}

function classMonster($monster)
{
    for ($i = 0; $i < count($monster); $i++) {
        $monsters[] = new Monster(
            $monster[$i][0]['monster_name'],
            $monster[$i][0]['monster_life'],
            $monster[$i][0]['monster_force'],
            $monster[$i][0]['monster_level'],
            $monster[$i][0]['experience_gain']
        );
    }

    return $monsters;
}

$character = classChar($characterUser, $bdd);

$game = new Game($character);

$log = $bdd->getLog();
$time = new DateTime($log[0]['date']);
$time->add(new DateInterval('PT' . $log[0]['fight_time'] . 'S'));

$currentTime = new Datetime(date("Y-m-d H:i:s"));

$interval = date_diff($time, $currentTime);


//alternative 2

function test($zone){
    return explode(",", $zone);
}


//on créé une liste pour la zone en question
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Aventure</h1>
    <ul>
        <li><a href="../index.php">Accueil</a></li>
        <li><a href="recrutement.php">Taverne</a></li>
    </ul>

    <h2>Combattre un monstre</h2>

    <?php
    if ($interval->invert == 1) {
        echo '<p>Votre groupe est en combat pendant encore : ' . $interval->h . ' heures, ' . $interval->m . ' minutes et ' . $interval->s . ' secondes. </p>';
    } else {

        $listZone = $bdd->getListZone('Débutant');
        
        for ($i = 0; $i < count($listZone); $i++) {

            //Affiche le form si le groupe n'est pas le même que l'ancien groupe
            $form = "
            <h3>{$listZone[$i]['zone_name']}</h3>
            <form action=\"\" method=\"post\">
                <input name=\"listZone\" type=\"hidden\" id=\"listZone\" value=\"{$listZone[$i]['zone_list_monster']}\">
                <input type=\"submit\" value=\"Combattre\">
            </form>
            ";

            echo $form;

        }
    }
    
    ?>

    <?php
    if (isset($_POST['listZone']) && !empty($_POST['listZone'])) 
        {
            $monsters = $bdd->searchMonster(test($_POST['listZone']));
        
            $listClassMonster = classMonster($monsters);
            //update log && update personnage
            $result = $game->fight($listClassMonster);
            $bdd->updateLog($result[0], $_SESSION['id'], $game->getFightTime());
            $bdd->updateExperienceCharacter($result[1], $result[2]);

            header('Location: ' . $_SERVER["REQUEST_URI"]);
        }

    ?>

    <!-- log -->

    <?php
    //$log = $bdd->getLog();
    if ($log != null && $interval->invert != 1) {
    ?>

        <h2>Log du dernier combat</h2>

        <?= $log[0]['log']; ?>

    <?php
    }
    ?>

</body>

</html>