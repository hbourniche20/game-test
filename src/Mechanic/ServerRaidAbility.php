<?php

namespace App\Mechanic;

class ServerRaidAbility
{

    public $abilityData;
    public $currentTurnRecharge;
    public ServerRaidCharacter $possessor;

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

    public function execute(){
        // use skill and reset cooldown
        $abEffect = $this->abilityData['data']['ability_effect'];

        $this->currentTurnRecharge = intval($abEffect['effect_cooldown']);

        // Depend of the skill now

        $abEffecfId = $abEffect['effect_id'];
        $abEffecfScope = $abEffect['effect_scope'];

        if ($abEffecfId == 1){
            // give charge bar to all character
            foreach ($this->possessor->getRaidTeam()->getCharacterList() as $character){
                if ($abEffecfScope != "caster" || $abEffecfScope === "caster" && $character->charSlug == $this->possessor->charSlug) {
                    // modifiy charbar
                    $newCharbarValue = $character->getCurrentAttr('chargeBar') + intval($abEffect['effect_value']);
                    $newCharbarValue = GameHelper::clamp($newCharbarValue,0, $character->getMaxAttr('chargeBar'));

                    // apply
                    $character->setCurrentAttr('chargeBar', $newCharbarValue);
                }
            }
        }
    }

}