<?php

namespace App\Mechanic;

use App\Mechanic\Scenario\ScenarioHelper;

class ServerRaidGameManager
{

    public static $serverStaticRessource;

    public static function init(){
        self::$serverStaticRessource = new ServerStaticRessource();
    }

    public static function makeMessageTurnCallback($message){
        $response = [];
        $response['action'] = 'message';
        $response['turnResult'] = [];
        $response['scenarioListData'] = [];
        $response['message'] = $message;
        // Generally it"s a error send only to himself
        $response['sendOnlyFormHimself'] = true;

        return $response;
    }

    /**
     * Refresh all player info in front
     * @param ServerRaidGame $raidGame
     * @param null $charSlug
     * @param array $scenario
     * @return array|mixed
     */
    public static function getActualRaidStateScenario(ServerRaidGame $raidGame, $charSlug=null, $scenario=[]){
        if ($charSlug != null){
            $scenario[RaidGameConstants::$SCN_CSL_KEY] = ScenarioHelper::buildSkillStateResultScenario([$raidGame->getCharacter($charSlug)]);
        } else {
            $scenario[RaidGameConstants::$SCN_CSL_KEY] = ScenarioHelper::buildSkillStateResultScenario($raidGame->getPlayerRaidTeam()->getCharacterList());
        }
        //
        $scenario[RaidGameConstants::$SCN_STT_KEY] = ScenarioHelper::buildPlayerRefreshStatutScenario($raidGame);

        return $scenario;
    }
    /**
     * Push player ready prepared action
     * @param $charSlug
     * @param $superAttack
     * @return bool
     */
    public static function pushReadyPlayer(ServerRaidGame $raid, $charSlug, $superAttack) : bool{
        // Only if was not ready in this turn
        if (!array_key_exists($charSlug, $raid->playerActionPrepare)) {
            // this char are ready
            $raid->playerActionPrepare[$charSlug] = ['attack_kind' => $superAttack];
            // increment
            $raid->countReadyPlayer += 1;

            // If all ready
            if ($raid->getPlayerRaidTeam()->isAllTurnReady($raid->countReadyPlayer)) {
                return true;
            }
        }

        return false;
    }

    public static function makeFighterActionAbility($charSlug, $name='attack'){
        $d = ['moveAnim' => [
            'char' => ['funcName' => $name]
        ]];

        // For gayen for example
        if ($name === "mortal_A") {
            $d['moveAnim']['ab'] = ['index' => 0, 'funcName' => 'nsp_3040298000_02_s2', 'fullScreen' => true];
        }

        return $d;
    }

    public static function bossChooseRandomTarget(ServerRaidGame $raidGame){
        $tmpArray = [];
        foreach ($raidGame->characterList as $character){
            if ($character->charSlug != "Boss"){
                array_push($tmpArray, $character);
            }
        }
        // Finally get rand
        return $tmpArray[random_int(0, count($tmpArray) - 1)];
    }

    public static function processBossTurn(ServerRaidGame $raidGame, $turnData, ServerRaidBossChar $character, $charSlug){
        // boss choose random target
        $targetSlug = self::bossChooseRandomTarget($raidGame)->charSlug;
        $targetChar = $raidGame->getCharacter($targetSlug);

        if ($character->canMortal()) {
            // Mortal attack
            $choosenMortal = $character->chooseRandomMortal();
            $damageStepResult = $character->applyMortalDamage($choosenMortal, $targetChar);
            array_push($turnData['scenarioListData'], ScenarioHelper::buildBossMortalScenario($raidGame, $charSlug, $targetSlug, $choosenMortal, $damageStepResult));
        } else {
            // Normal attack
            $damageStepResult = $character->execAttackNormal($targetChar);
            array_push($turnData['scenarioListData'], ScenarioHelper::buildNormalAttackScenario($raidGame, $charSlug, $targetSlug, $damageStepResult));
        }
        return $turnData;
    }

    public static function processPlayerTurn(ServerRaidGame $raidGame, $turnData, ServerRaidCharacter $character, $charSlug){
        // Target
        $targetSlug = "Boss";
        // Process in data version and prepare scenario
        $targetChar = $raidGame->getCharacter($targetSlug);

        // Super Attack
        if ($raidGame->playerActionPrepare[$charSlug]["attack_kind"]){
            $damageStepResult = $character->execOugiAttack($targetChar);
            array_push($turnData['scenarioListData'], ScenarioHelper::buildPlayerOugiScenario($raidGame, $charSlug, $targetSlug, $damageStepResult));
        }
        // Normal Attack
        else {
            $damageStepResult = $character->execAttackNormal($targetChar);
            array_push($turnData['scenarioListData'], ScenarioHelper::buildNormalAttackScenario($raidGame, $charSlug, $targetSlug, $damageStepResult));
        }

        return $turnData;
    }

    public static function processPlayerSkill(ServerRaidGame $raidGame, $charSlug, $abIndex){
        // Prepare turn data
        $response = [];
        $response['action'] = 'exec_skill';
        $response['turnResult'] = [];
        $response['scenarioListData'] = [];

        // Target
        $targetSlug = "Boss";

        // Process in data version and prepare scenario
        $character = $raidGame->getCharacter($charSlug);
        $targetChar = $raidGame->getCharacter($targetSlug);

        // Get skill
        $ability = $character->abilityList['skill_'. $abIndex];
        // process only if the skill is avlaible
        if ($ability->isReady()) {
            // execute skill process
            $character->execSkill($abIndex);

            // Mock
            $damageStepResult = [];

            // Player Skill
            $scenarioData = ScenarioHelper::buildPlayerSkillScenario($raidGame, $charSlug, $targetSlug, $ability, $damageStepResult);
            // Refresh statut Scneario
            $scenarioData[RaidGameConstants::$SCN_STT_KEY] = ScenarioHelper::buildPlayerRefreshStatutScenario($raidGame);

            // Skill Scenario State
            array_push($response['scenarioListData'], $scenarioData);
        }
        else {
            $response = self::makeMessageTurnCallback("Vous ne pouvez pas utiliser cette compétence pour le moment");
        }
        return $response;
    }

    public static function makeTurnBattle(ServerRaidGame $raidGame){
        // Prepare turn data
        $turnData = [];
        $turnData['action'] = 'exec_turn_result';
        $turnData['turnResult'] = [];
        $turnData['scenarioListData'] = [];

        // Player
        foreach ($raidGame->characterList as $character){
            // Get char slug
            $charSlug = $character->charSlug;

            if ($charSlug != "Boss"){
                $turnData = self::processPlayerTurn($raidGame, $turnData, $character, $charSlug);
            }
            else {
                $turnData = self::processBossTurn($raidGame, $turnData, $character, $charSlug);
            }
        }
        // Clear turn
        $raidGame->clearTurn();
        // Count dow
        foreach ($raidGame->getAllCharacterList() as $character){
            $character->countdownTurn();
        }

        // After each turn need to refresh statut as last queue
        array_push($turnData['scenarioListData'], ServerRaidGameManager::getActualRaidStateScenario($raidGame));

        //$response['charSlug'] = $userIndex;
        return $turnData;
    }
}