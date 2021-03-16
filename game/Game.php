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

    public function __construct($characterGroup){
        $this->characterGroup = $characterGroup;
    }

    public function fight($monsterGroup){

        //todo - Les monstres doivent taper avec des dégat différent que 1
        //todo - Les monstres commencent à avoir plusieurs compétence 
        //todo - regrouper les deux boucles for du combat en une seule

        $this->setMonsterGroup($monsterGroup);
        $round = 0;

        while($this->inFight){

            //$this->{$this->variableCalled} permet d'appeler un attribut de class dynamiquement
            for ($i = 0; $i < count($this->{$this->turnNameGroup}); $i++){

                //On vérifie si le personnage est en vie et après on tape le monstre

                if ($this->{$this->turnNameGroup}[$i]->getAlive()){
                    $this->{$this->turnNameGroup}[$i]->searchCible($this->{$this->targetNameGroup});
                    $this->log = $this->log . $this->{$this->targetNameGroup}[$this->{$this->turnNameGroup}[$i]->getCible()]->takeDmg($this->{$this->turnNameGroup}[$i]->getForce(), $this->{$this->turnNameGroup}[$i]->getName());
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

            //Le personnage doit cibler un monstre en question et il doit recevoir des dégats

            /* [obsolete]
            for ($i = 0; $i < count($this->characterGroup); $i++){

                //On vérifie si le personnage est en vie et après on tape le monstre

                if ($this->characterGroup[$i]->getAlive()){
                    $this->characterGroup[$i]->searchCible($this->monsterGroup);
                    $this->log = $this->log . $this->monsterGroup[$this->characterGroup[$i]->getCible()]->takeDmg($this->characterGroup[$i]->getCharForce(), $this->characterGroup[$i]->getCharName());
                }

                //On vérifie si le groupe du monstre est encore en vie

                $this->countAlive("getMonsterLife", "monsterGroup", "setMonsterGroupInLife");
                $this->countAlive("getCharLife", "characterGroup", "setCharacterGroupInLife");

                //Si le groupe du monstre est mort, alors on envoie les logs

                if ($this->nbCharacterGroupInLife == 0 || $this->nbMonsterGroupInLife == 0){
                    $this->log = $this->log . "Votre groupe à battu les monstres, vous gagnez de l'expérience.<br>";
                    $this->fightTime = $round * 2;
                    $this->calculExperience();
                    $this->checkCharacterInLife();

                return [$this->log, $this->experienceGain, $this->characterInLife];
                }
                
            }

            for ($i = 0; $i < count($this->monsterGroup); $i++){

                //Le monstre tape le personnage

                if ($this->monsterGroup[$i]->getAlive()){
                    $this->monsterGroup[$i]->searchCible($this->characterGroup);
                    $this->log = $this->log . $this->characterGroup[$this->monsterGroup[$i]->getCible()]->takeDmg(1, $this->monsterGroup[$i]->getMonsterName());
                }

                //On vérifie si le groupe du personnage est encore en vie

                $this->countAlive("getMonsterLife", "monsterGroup", "setMonsterGroupInLife");
                $this->countAlive("getCharLife", "characterGroup", "setCharacterGroupInLife");

                //Si le groupe du personnage est mort, alors on envoie les logs

                if ($this->nbCharacterGroupInLife == 0 || $this->nbMonsterGroupInLife == 0){
                    $this->log = $this->log . "Votre groupe à échoué, vous gagnez seulement la moitié d'expérience des monstres tué.<br>";
                    $this->fightTime = $round * 2;
                    $this->calculExperience();
                    $this->getAllCharacterId();
                    
                    return [$this->log, $this->experienceGain / 2, $this->allCharacterId];
                }
                
            }*/

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
}