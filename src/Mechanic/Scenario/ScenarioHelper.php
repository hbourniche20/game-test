<?php

namespace App\Mechanic\Scenario;

use App\Mechanic\GameHelper;
use App\Mechanic\RaidGameConstants;
use App\Mechanic\ServerRaidCharacter;
use App\Mechanic\ServerRaidGame;
use App\Mechanic\ServerRaidGameManager;

class ScenarioHelper {

    public static function buildFromScenarioTemplate($template, $templateData, $templateExtraData=null){
        // Extra data dynamic
        if ($templateExtraData != null){
            if (array_key_exists('soundPosList', $templateExtraData)){
                foreach ($templateExtraData['soundPosList'] as $extraData) {
                    $newData = ["pos" => $extraData["pos"]];
                    $newData["sound"] = ["soundId" => $extraData["soundId"]];
                    // v 1.0 override pos
                    array_push($template['data']['frames'], $newData);
                }
            }
        }

        //
        $templateStringRaw = json_encode($template);

        foreach(array_keys($templateData) as $dataKey){
            $templateStringRaw = strtr(
                $templateStringRaw,
                [$dataKey => $templateData[$dataKey]]
            );
        }

        return json_decode($templateStringRaw, true);
    }


    public static function buildCharAnim($charSlug, $name){
        return [
            'charSlug' => $charSlug,
            'funcName' => $name
        ];
    }

    public static function builDamage(ServerRaidGame $raidGame, $charSlug, $damageStep){
        return [
            'charSlug' => $charSlug,
            'value' => $damageStep['damage'],
            'remainPercent' => $damageStep['remainPercent']. '%',
            'position' => $raidGame->getCharacter($charSlug)->getRandomDamagePos()
        ];
    }

    public static function buildFrameScenario($frame){
        return [
          'pos' => $frame
        ];
    }

    /**
     * Check if minial data is valid else create empty
     * @param $scenarioData
     * @param ServerRaidCharacter $character
     */
    public static function checkCharStateScenario($scenarioData, ServerRaidCharacter $character){
        if (!array_key_exists($character->charSlug, $scenarioData)){
            $scenarioData[$character->charSlug] = [];
        }
        return $scenarioData;
    }

    /**
     * Check if minial data is valid else create empty
     * @param $scenarioData
     * @param ServerRaidCharacter $character
     */
    public static function checkCharStatutScenario($scenarioData, ServerRaidCharacter $character){
        if (!array_key_exists($character->charSlug, $scenarioData)){
            $scenarioData[$character->charSlug] = [];
        }

        return $scenarioData;
    }

    public static function buildPlayerRefreshStatutScenario(ServerRaidGame $raidGame){
        $scenarioData = [];

        // For each all team
        foreach ( $raidGame->getAllRaidTeam() as $raidTeam){
            foreach ($raidTeam->getCharacterList() as $character){
                // Check validation
                $scenarioData = self::checkCharStatutScenario($scenarioData, $character);
                // Prepare
                $scenarioData[$character->charSlug]['statut'] = [];
                // Hp
                $hpPercent = $character->getAttrPercent('hp');
                $scenarioData[$character->charSlug]['statut']['hpPercent'] = $hpPercent .'%';
                $scenarioData[$character->charSlug]['statut']['hp'] = $character->getCurrentAttr('hp');
                // Char bar
                $cBPercent = $character->getAttrPercent('chargeBar');
                $scenarioData[$character->charSlug]['statut']['chargeBarPercent'] = $cBPercent. '%';
                $scenarioData[$character->charSlug]['statut']['chargeBar'] = $character->getCurrentAttr('chargeBar');
                // If die
                $scenarioData[$character->charSlug]['statut']['alive'] = $character->isAlive();
            }
        }

        return $scenarioData;
    }


    public static function buildSkillStateResultScenario($characterList){
        // Check validation
        $scenarioData = [];

        foreach ($characterList as $character) {
            $scenarioData = self::checkCharStateScenario($scenarioData, $character);

            // Prepare
            $scenarioData[$character->charSlug]['skills'] = [];

            $i = 0;
            // Check skill avalaible
            foreach ($character->getAbilitySkillList() as $aSkill) {
                // hydrate data
                $scenarioData[$character->charSlug]['skills'][$i] = [];
                $scenarioData[$character->charSlug]['skills'][$i]['available'] = $aSkill->isReady();
                $scenarioData[$character->charSlug]['skills'][$i]['remainingTurn'] = $aSkill->currentTurnRecharge;
                // increment
                $i++;
            }
        }

        return $scenarioData;
    }

    public static function buildBossMortalScenario(ServerRaidGame $raidGame, $charSlug, $targetSlug, $choosenMortal, $damageStepResult){
        // Prepare local data
        $scenarioData = [];

        //
        $character = $raidGame->getCharacter($charSlug);
        $targetChar = $raidGame->getCharacter($targetSlug);
        // Take first ougi ability by default
        $ability = $character->abilityList[$choosenMortal];

        // Get scenario template
        $template_id = $ability->abilityData['data']['scenario_template'];
        $scenarioTemplate = ServerRaidGameManager::$serverStaticRessource->scenarioTemplate[$template_id];
        $scenarioData = $ability->abilityData['data']['scenario_data'];
        // safe extra data
        $scenarioExtraData = GameHelper::safeArrayElement('scenario_extra_data', $ability->abilityData['data']);
        // override
        $scenarioData[':casterSlug'] = $charSlug;
        $scenarioData[':targetSlug'] = $targetSlug;
        $scenarioData[':duration'] = $ability->abilityData['data']['duration'];
        // -- damage part
        $scenarioData[':damageValue'] = $damageStepResult[0]['damage'];
        $scenarioData[':remainHPPercent'] =  $damageStepResult[0]['remainPercent']. '%';
        $scenarioData[':hpAfterDamage'] = $damageStepResult[0]['remainHP'];

        // -- pos
        $scenarioData[':damageCharPosX'] = $targetChar->getRandomDamagePos()[0];
        $scenarioData[':damageCharPosY'] = $targetChar->getRandomDamagePos()[1];
        // -- default self wait anim
        $scenarioData[':waitAnim'] = $character->getAnimPos(RaidGameConstants::$ANM_WAIT);

        // Animation attack
        $finalScenario = self::buildFromScenarioTemplate($scenarioTemplate, $scenarioData, $scenarioExtraData);

        // If dead
        /*
        if (!$targetChar->isAlive()){
            $sT=ServerRaidGameManager::$serverStaticRessource->scenarioTemplate["player_dead"];
            $sD = [
                ":casterSlug" => $targetChar->charSlug
            ];
            $finalScenario = self::buildFromScenarioTemplate($sT, $sD);

        }
        */

        return $finalScenario['data'];
    }

    public static function buildPlayerSkillScenario(ServerRaidGame $raidGame, $charSlug, $targetSlug, $ability, $damageStepResult){
        // Prepare local data
        $scenarioData = [];

        // Char
        $character = $raidGame->getCharacter($charSlug);
        $targetChar = $raidGame->getCharacter($targetSlug);

        // Get scenario template
        $template_id = $ability->abilityData['data']['scenario_template'];
        $scenarioData = $ability->abilityData['data']['scenario_data'];
        $scenarioTemplate = ServerRaidGameManager::$serverStaticRessource->scenarioTemplate[$template_id];
        // safe extra data
        $scenarioExtraData = GameHelper::safeArrayElement('scenario_extra_data', $ability->abilityData['data']);
        // override
        $scenarioData[':casterSlug'] = $charSlug;
        $scenarioData[':targetSlug'] = $targetSlug;
        $scenarioData[':duration'] = $ability->abilityData['data']['duration'];
        // -- damage part
        if (count($damageStepResult) > 0) {
            $scenarioData[':damageValue'] = $damageStepResult[0]['damage'];
            $scenarioData[':remainHPPercent'] = $damageStepResult[0]['remainPercent'] . '%';
            $scenarioData[':hpAfterDamage'] = $damageStepResult[0]['remainHP'];
        }
        // -- pos
        $scenarioData[':damageCharPosX'] = $targetChar->getRandomDamagePos()[0];
        $scenarioData[':damageCharPosY'] = $targetChar->getRandomDamagePos()[1];

        $finalScenario = self::buildFromScenarioTemplate($scenarioTemplate, $scenarioData, $scenarioExtraData);

        // ++ Add dynamic auto character state
        $finalScenario[RaidGameConstants::$SCN_DATA_KEY][RaidGameConstants::$SCN_CSL_KEY] = self::buildSkillStateResultScenario([$character]);

        // Finally return
        return $finalScenario['data'];
    }

    public static function buildPlayerOugiScenario(ServerRaidGame $raidGame, $charSlug, $targetSlug, $damageStepResult){
        // Prepare local data
        $scenarioData = [];

        //
        $character = $raidGame->getCharacter($charSlug);
        $targetChar = $raidGame->getCharacter($targetSlug);
        // Take first ougi ability by default
        $ability = $character->abilityList['player_ougi_0'];

        // Get scenario template
        $template_id = $ability->abilityData['data']['scenario_template'];
        $scenarioTemplate = ServerRaidGameManager::$serverStaticRessource->scenarioTemplate[$template_id];
        $scenarioData = $ability->abilityData['data']['scenario_data'];
        // safe extra data
        $scenarioExtraData = GameHelper::safeArrayElement('scenario_extra_data', $ability->abilityData['data']);
        // override
        $scenarioData[':casterSlug'] = $charSlug;
        $scenarioData[':targetSlug'] = $targetSlug;
        $scenarioData[':duration'] = $ability->abilityData['data']['duration'];
        // -- damage part
        $scenarioData[':damageValue'] = $damageStepResult[0]['damage'];
        $scenarioData[':remainHPPercent'] =  $damageStepResult[0]['remainPercent']. '%';
        $scenarioData[':hpAfterDamage'] = $damageStepResult[0]['remainHP'];
        // -- pos
        $scenarioData[':damageCharPosX'] = $targetChar->getRandomDamagePos()[0];
        $scenarioData[':damageCharPosY'] = $targetChar->getRandomDamagePos()[1];

        $finalScenario = self::buildFromScenarioTemplate($scenarioTemplate, $scenarioData, $scenarioExtraData);

        return $finalScenario['data'];
    }

    /**
     * @param $charSlug
     * @param $targetSlug
     * @param $countAtk
     * @return array
     */
    public static function buildNormalAttackScenario(ServerRaidGame $raidGame, $charSlug, $targetSlug, $damageStepResult){
        // Prepare local data
        $scenarioData = [];
        $scenarioData['frames'] = [];

        $atkPos = [5, 16,28];
        $atkName = ['attack', 'double', 'triple'];
        $lastPos = 0;

        // Make attack for each frame
        for ($i = 0; $i < count($damageStepResult); $i++){
            // attack anim
            $frameAttack = self::buildFrameScenario($atkPos[$i]);
            $frameAttack['charAnim'] = self::buildCharAnim($charSlug, $atkName[$i]);

            // Hit/damage effect
            $frameDamage = self::buildFrameScenario($atkPos[$i] + 3);
            $frameDamage['charAnim'] = self::buildCharAnim($targetSlug, 'damage');
            $frameDamage['damage'] = self::builDamage($raidGame, $targetSlug, $damageStepResult[$i]);
            // store
            $lastPos = $atkPos[$i];

            // push in scenario
            array_push($scenarioData['frames'], $frameAttack, $frameDamage);
        }
        // Finally
        $frameTargetRestore = self::buildFrameScenario($lastPos + 8);
        $frameTargetRestore['charAnim'] = self::buildCharAnim($targetSlug, $raidGame->getCharacter($targetSlug)->getAnimPos(RaidGameConstants::$ANM_WAIT));
        // rsestor my self
        $frameSelfRestore = self::buildFrameScenario($lastPos + 25);
        $frameSelfRestore['charAnim'] = self::buildCharAnim($charSlug, $raidGame->getCharacter($charSlug)->getAnimPos(RaidGameConstants::$ANM_WAIT));

        // push in scenario
        array_push($scenarioData['frames'], $frameTargetRestore, $frameSelfRestore);

        // Duration
        $scenarioData['info'] = ['duration' => (($lastPos + 25) * 34) + 30];

        // Return the final scenario data
        return $scenarioData;
    }
}