<?php

class User {
    private $id;
    private $pseudo;
    private $email;
    private $nbCharacter;

    public function __construct($id, $pseudo, $email){
        $this->id = $id;
        $this->pseudo = $pseudo;
        $this->email = $email;
    }

    public function getId(){
        return $this->id;
    }

    public function getPseudo(){
        return $this->pseudo;
    }

    public function getEmail(){
        return $this->email;
    }

}