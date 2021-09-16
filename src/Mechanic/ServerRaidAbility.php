<?php

namespace App\Mechanic;

class ServerRaidAbility
{

    public $abilityData;
    public $currentTurnRecharge;
    public $possessor;

    public function __construct($abilityData, $possessor){
        $this->currentTurnRecharge = 0;
        $this->abilityData = $abilityData;
        $this->possessor = $possessor;
    }

    public function isReady(){
        return ($this->currentTurnRecharge < 1);
    }

    public function countdownTurn($countdown = 1){
        // If not ready countdown again
        if (!$this->isReady()){
            $this->currentTurnRecharge -= $countdown;
        }
    }

    public function execute()
    {
        // handle skill effect
        AbilityCustomEffect::handleSkillEffect($this);
    }

}