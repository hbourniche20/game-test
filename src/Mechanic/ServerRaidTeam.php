<?php

namespace App\Mechanic;

class ServerRaidTeam
{

    protected $characterList;
    protected $aliveCharacterList;

    public function __construct(){
        $this->characterList = [];
        $this->aliveCharacterList = [];
    }

    public function isAllTurnReady($count){
        return ($count >= count($this->getCharacterList()));
    }

    public function addCharacter(ServerRaidCharacter $character){
        $character->setRaidTeam($this);
        array_push($this->characterList, $character);
        array_push($this->aliveCharacterList, $character);
    }

    /**
     * TODO : FIX
     * @param $character
     */
    public function onCharacterDie($character){
        $this->aliveCharacterList = [];
        foreach ($this->characterList as $char){
            if ($char->isAlive()){
                array_push( $this->aliveCharacterList, $char);
            }
        }

    }
    /**
     * @param bool $onlyAlive
     * @return ServerRaidCharacter[]
     */
    public function getCharacterList($onlyAlive=true){
        return $this->characterList;
    }

}