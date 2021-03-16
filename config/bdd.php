<?php

class BDD {
    public $session = "off";
    private $pdo;
    private $data;

    public function __construct(){
        if ($this->session == "off") {
            $this->PDO();
        }
    }

    private function PDO(){
        try {
            $this->pdo = new PDO('mysql:host=localhost;dbname=game;charset=utf8', 'root', '');
            $this->session = "on";
        } catch (PDOException $e) {
            $this->pdo = 'Connexion échouée : ' . $e->getMessage();
        }
    }

    public function getPDO(){
        return $this->pdo;
    }

    public function prepareAccount($email, $type = null){
        $data = $this->pdo->prepare('SELECT * FROM user WHERE email = ?');
        $data->execute([
            $email
        ]);

        $data = $data->fetchAll();

        if ($type === "data"){
            return $data;
        }

        if ($type === "connexion"){
            if ($data == null){
                return "L'adresse mail ou le mot de passe est incorrect";
            }else {
                return $data;
            }
        }else {
            if ($data != null){
                return true;
            }else {
                return false;
            }
        }

    }

    public function addAccount($pseudo, $email, $password){
        $query = $this->pdo->prepare('INSERT INTO user (pseudo, email, password) VALUES (:pseudo, :email, :password)');
        $query->bindParam(':pseudo', $pseudo);
        $query->bindParam(':email', $email);
        $query->bindParam(':password', $password);
        $query->execute();
    }

    public function characterUser($id){
        $data = $this->pdo->prepare('SELECT * FROM user_character WHERE user_id = ?');
        $data->execute([
            $id
        ]);

        $data = $data->fetchAll();

        return $data;
    }

    public function addCharacter($id, $charClass, $charName){
        $query = $this->pdo->prepare('INSERT INTO user_character (user_id, char_class, char_name) VALUES (:user_id, :char_class, :char_name)');
        $query->bindParam(':user_id', $id);
        $query->bindParam(':char_class', $charClass);
        $query->bindParam(':char_name', $charName);
        $query->execute(); 
    }

    public function getStatsClass($charClass, $level){
        $data = $this->pdo->prepare('SELECT * FROM stats_character WHERE char_class = ? AND char_level = ?');
        $data->execute([
            $charClass,
            $level
        ]);

        $data = $data->fetchAll();

        return $data;
    }

    /*[obsolete]
    public function getListMonster(){
        $data = $this->pdo->query('SELECT * FROM monster');
        $data = $data->fetchAll();
        return $data;
    }*/

    /*[obsolete]
    public function getListMonsterGroup($group){
        $data = $this->pdo->prepare('SELECT * FROM monster WHERE groupe = ?');
        
        $data->execute([
            $group
        ]);
        $data = $data->fetchAll();
        return $data;
    }*/

    public function getLog(){
        $data = $this->pdo->query('SELECT * FROM log WHERE user_id = 1');
        $data = $data->fetchAll();
        return $data;
    }

    public function updateLog($log, $id, $fightTime){
        $query = $this->pdo->prepare('UPDATE log SET log = :log, date = NOW(), fight_time = :fight_time WHERE user_id = :user_id');
        $query->bindParam(':log', $log);
        $query->bindParam(':user_id', $id);
        $query->bindParam(':fight_time', $fightTime);
        $query->execute(); 
    }

    public function updateExperienceCharacter($experience, $character){
        for ($i = 0; $i < count($character); $i++){

            //$newExperience = $experience + $character[$i][1];

            $query = $this->pdo->prepare('UPDATE user_character SET char_experience = :charExperience, char_level = :charLevel WHERE id = :id');
            $query->bindParam(':charExperience', $character[$i][1]);
            $query->bindParam(':charLevel', $character[$i][2]);
            $query->bindParam(':id', $character[$i][0]);
            $query->execute(); 
        }
    }

    //On cherche la liste des monstres [alternative 2]
    public function searchMonster($listMonster){
        //reset le tableau à chaque fois qu'on lance la fonction
        $this->data = [];

        //pour chaque id de la liste alors on cherche ses information et on l'ajoute au tableau de monstre
        for ($i = 0; $i < count($listMonster); $i++){
            $data = $this->pdo->prepare('SELECT * FROM monster WHERE id = ?');
            $data->execute([$listMonster[$i]]);
            $this->data[$i] = $data->fetchAll();
        }

        return $this->data;
    }

    public function getListZone($zone){
        $data = $this->pdo->prepare('SELECT * FROM list_zone WHERE zone_category = ?');
        $data->execute([$zone]);
        return $data->fetchAll();
    }

    public function getNextLevel($level){
        $level++;
        $data = $this->pdo->prepare('SELECT char_experience_required FROM stats_character WHERE char_level = ?');
        $data->execute([$level]);
        $data = $data->fetchAll();
        if ($data == null){
            return 99999999999999;
        }

        return $data[0]['char_experience_required'];
    }

}