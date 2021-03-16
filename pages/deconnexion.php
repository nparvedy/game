<?php

session_start();
session_destroy();

$newStr = str_replace('pages/deconnexion.php', '', $_SERVER["REQUEST_URI"]);

header('Location: ' . $newStr);

//header('Location: ')