<?php

class UserConfig {
    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function getDataUser($email){
        return $this->pdo->prepareAccount($email, "data");
    }
}