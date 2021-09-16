<?php


namespace App\Mechanic;


class AbilityCustomEffect
{

    public static function handleSkillEffect($character){
        // use skill and reset cooldown
        $abEffect = $character->abilityData['data']['ability_effect'];

        $character->currentTurnRecharge = intval($abEffect['effect_cooldown']);

        // Depend of the skill now

        $abEffecfId = $abEffect['effect_id'];
        $abEffecfScope = $abEffect['effect_scope'];
        $abEffecfValue = intval($abEffect['effect_value']);

        // $abEffecfScope "caster" "all"
        // Donne charge bar
        if ($abEffecfId == 1) {
            // give charge bar to all character
            foreach ($character->possessor->getRaidTeam()->getCharacterList() as $character) {
                if ($abEffecfScope != "caster" || $abEffecfScope === "caster" && $character->charSlug == $character->possessor->charSlug) {
                    // modifiy charbar
                    $newCharbarValue = $character->getCurrentAttr('chargeBar') + intval($abEffect['effect_value']);
                    $newCharbarValue = GameHelper::clamp($newCharbarValue, 0, $character->getMaxAttr('chargeBar'));

                    // apply
                    $character->setCurrentAttr('chargeBar', $newCharbarValue);
                }
            }
        }
        // Effect 2 : Gain HP
        if ($abEffecfId == 2) {
            // give charge bar to all character
            foreach ($character->possessor->getRaidTeam()->getCharacterList() as $character) {
                if ($abEffecfScope != "caster" || $abEffecfScope === "caster" && $character->charSlug == $character->possessor->charSlug) {
                    // modifiy charbar
                    $newCharbarValue = $character->getCurrentAttr('chargeBar') + intval($abEffect['effect_value']);
                    $newCharbarValue = GameHelper::clamp($newCharbarValue, 0, $character->getMaxAttr('chargeBar'));

                    // apply
                    $character->gainCurrentAttr('hp', 200);
                }

            }
        }
        // 3
        if ($abEffecfId == 3) {
            $character->gainCurrentAttr('def', 4000);
        }
        // 4
        if ($abEffecfId == 4) {
            foreach ($character->getAbilitySkillList() as $skill) {
                $skill->countdownTurn(2);
            }
        }
        // 10
        if ($abEffecfId == 10){
            $chocolatine = $abEffect['effect_chocolatine'];
            // algo
        }
    }
}