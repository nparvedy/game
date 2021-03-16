<?php

//Force = dmg physique
//Dex = chance de critique et dégat critique
//Intelligence = dmg magic
//spirit = puissance soin

class Character {
    private $id;
    private $class;
    private $name;
    private $force;
    private $intelligence;
    private $dexterity;
    private $spirit;
    private $life;
    private $level;
    private $experience;
    private $cible = 0;
    private $alive = true;
    private $nextLevel;

    public function __construct($id, $class, $name, $force, $intelligence, $dexterity, $spirit, $life, $level, $experience, $nextLevel){
        $this->id = $id;
        $this->class = $class;
        $this->name = $name; 
        $this->force = $force; 
        $this->intelligence = $intelligence; 
        $this->dexterity = $dexterity; 
        $this->spirit = $spirit; 
        $this->life = $life; 
        $this->level = $level; 
        $this->experience = $experience;
        $this->nextLevel = $nextLevel;

    }

    //Function

    public function searchCible($listMonster){
        for ($i = 0; $i < count($listMonster); $i++){
            if ($listMonster[$i]->getLife() > 0){
                $this->cible = $i;
                return;
            }
        }
    }

    public function takeDmg($dmg, $name){
        $this->life = $this->life - $dmg;
        if ($this->life <= 0){
            $this->isDead();
            return $name . " tape " . $this->name . ". Le personnage n'a plus de vie et meurt. <br>";
        }
        return $name . " tape " . $this->name . ". Il lui reste " . $this->life . " point de vie. <br>";
    }

    public function isDead(){
        $this->life = 0;
        $this->alive = false;
    }

    //getter

    public function getClass(){
        return $this->class; 
    }

    public function getName(){
        return $this->name; 
    }

    public function getForce(){
        return $this->force; 
    }

    public function getIntelligence(){
        return $this->intelligence; 
    }

    public function getDexterity(){
        return $this->dexterity; 
    }

    public function getSpirit(){
        return $this->spirit; 
    }

    public function getLife(){
        return $this->life; 
    }

    public function getLevel(){
        return $this->level; 
    }

    public function getExperience(){
        return $this->experience; 
    }

    public function getCible(){
        return $this->cible;
    }

    public function getAlive(){
        return $this->alive;
    }

    public function getId(){
        return $this->id;
    }

    public function getNextLevel(){
        return $this->nextLevel;
    }

    //setter

    public function setName(){
        //$this->charName = ; 
    }

    public function setForce(){
        //$this->charForce = ; 
    }

    public function setIntelligence(){
        //$this->intelligence = ; 
    }

    public function setDexterity(){
        //$this->dexterity = ; 
    }

    public function setSpirit(){
        //$this->spirit = ; 
    }

    public function setLife($vie){
        $this->life = $vie; 
    }

    public function setExperience($experience){
        $this->experience = $this->experience + $experience;
    }

    public function setLevel(){
       $this->level++;
    }
}