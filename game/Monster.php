<?php

class Monster {
    private $name;
    private $life;
    private $force;
    private $level;
    private $experienceGain;
    private $cible = 0;
    private $multiCible;
    private $alive = true;
    private $distanceNeeded = 10;

    public function __construct($name, $life, $force, $level, $experienceGain){
        $this->name = $name;
        $this->life = $life;
        $this->force = $force;
        $this->level = $level;
        $this->experienceGain = $experienceGain;
    }

    // function 

    public function isDead(){
        $this->charLife = 0;
        $this->alive = false;
    }

    //getter

    public function getLife(){
        return $this->life;
    }

    public function getName(){
        return $this->name;
    }

    public function getCible(){
        return $this->cible;
    }

    public function getAlive(){
        return $this->alive;
    }

    public function getExperience(){
        return $this->experienceGain;
    }

    public function getForce(){
        return $this->force; 
    }

    public function getDistanceNeeded(){
        return $this->distanceNeeded;
    }

    //setter

    public function setLife($vie){
        
    }

    public function takeDmg($dmg, $name){
        $this->life = $this->life - $dmg;
        if ($this->life <= 0){
            $this->isDead();
            return $name . " tape " . $this->name . ". Le monstre n'a plus de vie et meurt. <br>";
        }
        return $name . " tape " . $this->name . ". Il lui reste " . $this->life . " point de vie. <br>";
    }

    public function searchCible($listCharacter){
        for ($i = 0; $i < count($listCharacter); $i++){
            if ($listCharacter[$i]->getLife() > 0){
                $this->cible = $i;
                return;
            }
        }
    }


    
}