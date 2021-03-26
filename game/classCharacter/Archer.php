<?php

class Archer extends Character{
    protected $distanceNeeded = 10;
    protected $skillAoe = [['pluie de flèches', 0.3]];
    protected $nameAoeOne = 'Pluie de flèches';

    //si la fonction existe, alors l'utilisateur vas pouvoir lancer une compétences aoe, attention, ne pas supprimer
    public function aoeDmg($nbSkillAoe){
        return round($this->getForce() * $this->skillAoe[$nbSkillAoe][1]);
    }

    public function getNameAoe($nbSkillAoe){
        return $this->skillAoe[$nbSkillAoe][0];
    }
}