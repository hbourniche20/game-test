<?php


namespace App\Mechanic;

use MongoDB\Driver\Server;

class AbilityCustomEffect
{

    public static function handleSkillEffect(ServerRaidAbility $ability, $raidGame){
        // use skill and reset cooldown
        $abEffect = $ability->abilityData['data']['ability_effect'];

        $ability->currentTurnRecharge = intval($abEffect['effect_cooldown']);

        // Depend of the skill now
        $abEffecfId = $abEffect['effect_id'];
        $abEffecfScope = $abEffect['effect_scope'];
        $abEffecfValue = intval($abEffect['effect_value']);

        // Characters
        $teamList = $ability->possessor->getRaidTeam()->getCharacterList();
        $bossCharacter = $raidGame->getBossRaidTeam()->getCharacterList()[0]; // TODO Check if there is no other bosses

        switch($abEffecfId) {
            case 1:
                AbilityCustomEffect::fillChargeBar($teamList, $abEffecfScope, $abEffecfValue);
                break;
            case 2:
                AbilityCustomEffect::heal($teamList, $abEffecfScope, $abEffecfValue);
                break;
            case 3:
                AbilityCustomEffect::reduceCooldown($teamList, $abEffecfScope, $abEffecfValue);
                break;
            case 4:
                AbilityCustomEffect::sacrifice($ability->possessor, $teamList, $bossCharacter, $abEffecfScope, $abEffecfValue);
                break;
            case 5:
                AbilityCustomEffect::buffDamages($teamList, $abEffecfScope, $abEffecfValue);
                break;
            case 6:
                AbilityCustomEffect::lifeSteal($ability->possessor, $teamList, $bossCharacter, $abEffecfScope, $abEffecfValue);
                break;
            case 7:
                AbilityCustomEffect::gambling($ability->possessor, $teamList, $bossCharacter, $abEffecfScope, $abEffecfValue);
            break;
            default:
                AbilityCustomEffect::dealDamages($bossCharacter, $abEffecfScope, $abEffecfValue);
                break;
        }
    }

    /**
     * Deal damages to the boss
     * @param $bossCharacter ServerRaidCharacter Boss character
     * @param $scope string 'all' or 'caster'
     * @param $value int damages to deal
     */
    public static function dealDamages(ServerRaidCharacter $bossCharacter, $scope, $value) {
        $bossCharacter->looseHP($value);
    }

    /**
     * Fill the charge bar of a mate
     * @param $characters array team
     * @param $scope string 'all' or 'caster'
     * @param $value int value to add to the charge bar
     */
    public static function fillChargeBar($characters, $scope, $value) {
        foreach ($characters as $character) {
            if ($scope != "caster" || $scope === "caster" && $character->charSlug == $character->possessor->charSlug) {
                // modifiy charbar
                $newCharbarValue = $character->getCurrentAttr('chargeBar') + $value;
                $newCharbarValue = GameHelper::clamp($newCharbarValue, 0, $character->getMaxAttr('chargeBar'));

                // apply
                $character->setCurrentAttr('chargeBar', $newCharbarValue);
            }
        }
    }

    /**
     * Heal team mates or caster
     * @param $characters array team
     * @param $scope string 'all' or 'caster'
     * @param $value int value to heal
     */
    public static function heal($characters, $scope, $value) {
        foreach ($characters as $character) {
            if ($scope != "caster" || $scope === "caster" && $character->charSlug == $character->possessor->charSlug) {
                // apply
                $character->looseHP($value);
            }
        }
    }

    /**
     * Reduce all skills cooldowns
     * @param $characters array team
     * @param $scope string 'all' or 'caster'
     * @param $value int nb of turns to reduce
     */
    public static function reduceCooldown($characters, $scope, $value) {
        foreach ($characters as $character) {
            if ($scope != "caster" || $scope === "caster" && $character->charSlug == $character->possessor->charSlug) {
                foreach ($character->abilityList as $ability) {
                    $ability->countdownTurn($value);
                }
            }
        }
    }

    /**
     * Tue le lanceur et selon le scope:
     * 'all': Buff, heal les alliés en fonction des attributs courant du lanceur multiplié par la $value
     * 'caster': Debuff, et deal des dommages au boss en fonction des attributs max du lanceur multiplié par la $value
     * @param $caster ServerRaidCharacter Spell Caster
     * @param $characters array team
     * @param $bossCharacter ServerRaidCharacter Boss character
     * @param $scope string 'all' or 'caster'
     * @param $value int multiplicator
     */
    public static function sacrifice($caster, $characters, $bossCharacter, $scope, $value) {

        if($scope == 'all') {
            foreach($characters as $character) {
                if($character->charSlug != $caster->charSlug) {
                    $character->setCurrentAttr('hp', $caster->getCurrentAttr('hp')*$value);
                    $character->setCurrentAttr('charge', $character->getMaxAttr('charge'));
                    $character->gainCurrentAttr('baseDamage', $caster->getCurrentAttr('baseDamage')*$value);
                }
            }
        } else {
            $bossCharacter->looseHP($caster->getMaxAttr('hp'*$value));
            $bossCharacter->looseCurrentAttr('baseDamage', $caster->getCurrentAttr('baseDamage')*$value);
            $bossCharacter->setCurrentAttr('charge', 0);
        }
        $caster->looseHP($caster->getCurrentAttr('hp'));
    }

    /**
     * Increase character damages by %
     * @param $characters array Team
     * @param $scope: string 'all' or 'caster'
     * @param $value: int % value to add to the basicDamages
     */
    public static function buffDamages($characters, $scope, $value) {
        foreach ($characters as $character) {
            if ($scope != "caster" || $scope === "caster" && $character->charSlug == $character->possessor->charSlug) {
                $character->gainCurrentAttr('baseDamage', ($value*$character->getCurrentAttr('baseDamage')) / $character->getMaxAttr('baseDamage'));
            }
        }
    }

    /**
     * Steal life from boss
     * @param $caster
     * @param $characters
     * @param $bossCharacter
     * @param $scope
     * @param int $value % of life steal
     */
    private static function lifeSteal($caster, $characters, $bossCharacter, $scope, int $value){

        $bossCharacter->looseHP($caster->getCurrentAttr('baseDamage'));
        foreach ($characters as $character) {
            if ($scope != "caster" || $scope === "caster" && $character->charSlug == $character->possessor->charSlug) {
                $character->gainCurrentAttr('hp', $character->getCurrentAttr('baseDamage')*($value/100));
            }
        }
    }

    /**
     * 9 chances over 10 to deal lot of damages to the boss
     * 1 chance over 10 to deal lot of damages to a mate
     * @param $caster
     * @param $characters
     * @param $bossCharacter
     * @param $scope
     * @param int $value Damage multiplicator
     * @throws \Exception
     */
    private static function gambling($caster, $characters, $bossCharacter, $scope, int $value) {
        $random = random_int(0, 9);
        $target = $bossCharacter;
        if($random == 1) {
            if($scope == "all") {
                $target = $characters[random_int(0, count($characters))];
            } else {
                $target = $caster;
            }
        }
        $target->looseHP($caster->getCurrentAttr('baseDamage')*$value);
    }

}
