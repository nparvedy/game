<?php

//Faire en sorte qu'après un combat, il y a un timer et qu'on ne peut pas relancer un autre combat tant que le timer n'est pas passé

class Game {

    private $characterGroup;
    private $monsterGroup;
    private $nbCharacterGroupInLife;
    private $nbMonsterGroupInLife;
    private $fightTime = 0;
    private $inFight = true;
    private $log;
    private $experienceGain;
    private $characterInLife;
    private $allCharacterId;
    private $turnNameGroup = 'characterGroup';
    private $targetNameGroup = 'monsterGroup';
    private $distanceCM;

    public function __construct($characterGroup){
        $this->characterGroup = $characterGroup;
    }

    public function fight($monsterGroup){

        //todo - Les monstres et personnages commencent à avoir plusieurs compétence 
        //todo - créer un système de distance entre character et monstres
        //todo - améliorer le système de distance

        $this->setMonsterGroup($monsterGroup);
        $round = 0;

        $this->initDistanceArray();

        while($this->inFight){

            //$this->{$this->variableCalled} permet d'appeler un attribut de class dynamiquement
            for ($i = 0; $i < count($this->{$this->turnNameGroup}); $i++){

                //On vérifie si le personnage est en vie et après on tape le monstre

                if ($this->{$this->turnNameGroup}[$i]->getAlive()){
                    $this->{$this->turnNameGroup}[$i]->searchCible($this->{$this->targetNameGroup});

                    //si c'est le tour des utilisateurs sinon tour des monstres
                    if ($this->turnNameGroup == 'characterGroup'){
                        //on vérifie si on a une compétence aoeDmg
                        if (method_exists($this->{$this->turnNameGroup}[$i], 'aoeDmg')){
                            //si c'est le cas, alors on vas taper toutes les cibles qui sont proches
                            //on récupère toutes les cibles à porter

                            $this->{$this->turnNameGroup}[$i]->resetMulticible();

                            for ($a = 0; $a < count($this->{$this->targetNameGroup}); $a++){
                                if ($this->{$this->turnNameGroup}[$i]->getDistanceNeeded() <= $this->distanceCM[$i][$this->{$this->turnNameGroup}[$i]->getCible()] && $this->{$this->targetNameGroup}[$a]->getLife() > 0){
                                    $this->{$this->turnNameGroup}[$i]->setMulticible($a);
                                }
                            }

                            $this->log = $this->log . $this->{$this->turnNameGroup}[$i]->getName() . ' décide de lancer ' . $this->{$this->turnNameGroup}[$i]->getNameAoe(0) . '.<br>';

                            $this->authorizedToHit($i, true);
                        }else {
                            //on compare la distance qu'à besoin le personnage pour taper et la distance entre le personnage et le monstre
                            if ($this->{$this->turnNameGroup}[$i]->getDistanceNeeded() <= $this->distanceCM[$i][$this->{$this->turnNameGroup}[$i]->getCible()]){
                                $this->authorizedToHit($i, false);
                            }
                        }
                        
                    }else {
                        if ($this->{$this->turnNameGroup}[$i]->getDistanceNeeded() <= $this->distanceCM[$this->{$this->turnNameGroup}[$i]->getCible()][$i]){
                            $this->authorizedToHit($i, false);
                        }
                    }
                    
                }

                //On vérifie si le groupe du monstre ou les personnages sont encore en vie

                $this->countAlive("getLife", "monsterGroup", "setMonsterGroupInLife");
                $this->countAlive("getLife", "characterGroup", "setCharacterGroupInLife");

                //Si le groupe du monstre est mort, alors on envoie les logs

                if ($this->nbCharacterGroupInLife == 0 || $this->nbMonsterGroupInLife == 0){
                    
                    $this->fightTime = $round * 2;
                    $this->calculExperience();

                    if ($this->nbMonsterGroupInLife == 0){
                        $this->log = $this->log . "Votre groupe à battu les monstres, vous gagnez de l'expérience.<br>";
                        $this->checkCharacterInLife();
                        return [$this->log, $this->experienceGain, $this->characterInLife];
                    }else {
                        $this->getAllCharacterId();
                        $this->log = $this->log . "Votre groupe à échoué, vous gagnez seulement la moitié d'expérience des monstres tué.<br>";
                        return [$this->log, $this->experienceGain / 2, $this->allCharacterId];
                    }

                
                }
                
            }

            //On change le tour du groupe

            if ($this->turnNameGroup == 'characterGroup'){
                $this->turnNameGroup = 'monsterGroup';
                $this->targetNameGroup = 'characterGroup';
            }else {
                $this->turnNameGroup = 'characterGroup';
                $this->targetNameGroup = 'monsterGroup';
            }
        

            $round++;

            if ($round == 100){
                $this->inFight = false;
            }

        }

        return $this->log;
    }

    public function setMonsterGroup($monsterGroup){
        $this->monsterGroup = $monsterGroup;
    }

    public function getFightTime(){
        return $this->fightTime;
    }

    public function countAlive($type, $group, $compteurType){
        $compteur = 0;
        for ($i = 0; $i < count($this->$group); $i++){
            if ($this->$group[$i]->$type() > 0){
                $compteur++;
            }

        }

        $this->$compteurType($compteur);
    }

    public function setMonsterGroupInLife($nb) {
        $this->nbMonsterGroupInLife = $nb;
    }

    public function setCharacterGroupInLife($nb){
        $this->nbCharacterGroupInLife = $nb;
    }

    public function calculExperience(){
        for ($i = 0; $i < count($this->monsterGroup); $i++){
            if ($this->monsterGroup[$i]->getAlive() == false){
                $this->experienceGain += $this->monsterGroup[$i]->getExperience();
            }
        }

    }

    public function checkCharacterInLife(){
        for ($i = 0; $i < count($this->characterGroup); $i++){
            if ($this->characterGroup[$i]->getAlive() == true){
                $this->levelUp($i);
                $this->characterInLife[] = [$this->characterGroup[$i]->getId(), $this->characterGroup[$i]->getExperience(), $this->characterGroup[$i]->getLevel()];
            }
        }
    }

    public function getAllCharacterId(){
        for ($i = 0; $i < count($this->characterGroup); $i++){
            $this->levelUp($i);
            $this->allCharacterId[] = [$this->characterGroup[$i]->getId(), $this->characterGroup[$i]->getExperience(), $this->characterGroup[$i]->getLevel()];
        }
    }

    public function getExperienceGain(){
        return $this->experienceGain;
    }

    public function levelUp($i){
        $this->characterGroup[$i]->setExperience($this->experienceGain);
        if ($this->characterGroup[$i]->getNextLevel() <= $this->characterGroup[$i]->getExperience()){
            $this->characterGroup[$i]->setLevel();
            $this->log = $this->log . $this->characterGroup[$i]->getName() . ' a gagné un niveau est passe au niveau ' . $this->characterGroup[$i]->getLevel() . '. <br>';
        }
        
    }

    //on initie la distance des monstres pour chaque personnages

    public function initDistanceArray(){
        for ($i = 0; $i < count($this->characterGroup); $i++){
            for ($a = 0; $a < count($this->monsterGroup); $a++){
                $this->distanceCM[$i][] = 10;
            }
        }
    }

    public function authorizedToHit($i, $isMulti){
        if ($isMulti){
            $dmgAoe = $this->{$this->turnNameGroup}[$i]->aoeDmg(0);
            for ($a = 0; $a < count($this->{$this->turnNameGroup}[$i]->getMulticible()); $a++){
                $this->log = $this->log . $this->{$this->targetNameGroup}[$this->{$this->turnNameGroup}[$i]->getMulticible()[$a]]->takeDmg($dmgAoe, $this->{$this->turnNameGroup}[$i]->getName());
            }
        }else {
            $this->log = $this->log . $this->{$this->targetNameGroup}[$this->{$this->turnNameGroup}[$i]->getCible()]->takeDmg($this->{$this->turnNameGroup}[$i]->getForce(), $this->{$this->turnNameGroup}[$i]->getName());
        }
    }
}