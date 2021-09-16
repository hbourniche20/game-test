<?php

namespace App\Mechanic;

use App\Entity\Ability;

class ServerRaidCharacter
{
    protected ServerRaidTeam $raidTeam;
    protected $maxAttribute;
    protected $currentAttribute;
    public $abilityList;
    public $aSkillList; // It'st just filtered ability
    public $charSlug;
    public $data;
    public $defaultWait;
    public $position;
    protected $isAlive;

    public function __construct($characterData, $playerIndex){
        // Default data
        $this->data = $characterData;
        $this->position = [0, 0];
        $this->aSkillList = [];
        $this->abilityList = [];
        $this->isAlive = true;

        // Store char slug
        if ($characterData['charPlayer']['user'] !== null){
            $this->charSlug = $characterData['charPlayer']['user']['slug'];
            $this->defaultWait = "stbwait";
            // position
            $xOffset = 0;

            // Num team player index
            if ($playerIndex % 2) {
                $xOffset =+ 75;
            }
            // Set origins
            $this->position[0] =  500 + $xOffset;
            $this->position[1] =  300 + (80 * $playerIndex);
        }
        else{
            $this->charSlug = "Boss";
            $this->defaultWait = "wait";
            // position
            $this->position[0] = $this->data['charPlayer']['classe']['libData']['origin']['x'];
            $this->position[1] = $this->data['charPlayer']['classe']['libData']['origin']['y'];
        }

        $this->maxAttribute = [];
        $this->currentAttribute = [];

        // Fill attribute from class and leveling
        $attrClassData = GameHelper::safeArrayElement('attributeBase', $this->data['charPlayer']['classe']['libData']);
        if ($attrClassData !== null){
            foreach ($attrClassData as $key => $attr){
                $this->currentAttribute[$key] = $attr;
                $this->maxAttribute[$key] = $attr;
                //Exception
                if ($key === "chargeBar"){
                    $this->currentAttribute[$key] = 0;
                }
            }
        // Default for test
        }else{
            // max
            $this->maxAttribute['hp'] = 17050000;
            $this->maxAttribute['chargeBar'] = 100;

            // attribute
            $this->currentAttribute['hp'] = 17050000;
            $this->currentAttribute['chargeBar'] = 0;
            $this->currentAttribute['baseDamage'] = 90000;
        }

        // Count by kind , usefull
        $countKind = [];
        $i = 0;
        foreach ($characterData['charPlayer']['classe']['abilityList'] as $abilityData){
            // Increment by kind
            $kind = $abilityData['kind'];

            // First time
            if (!array_key_exists($kind, $countKind)){
                $countKind[$kind] = 0;
            }

            // store ability
            $this->abilityList[$kind.'_'. $countKind[$kind]] = new ServerRaidAbility($abilityData, $this);

            // If skill store in skill to (memory reference
            if ($kind == "skill"){
                array_push($this->aSkillList, $this->abilityList[$kind.'_'. $countKind[$kind]]);
            }

            // increment
            $countKind[$kind] = $countKind[$kind]+1;
            $i++;
        }
    }

    /**
     * @return bool
     */
    public function isAlive(): bool
    {
        return $this->isAlive;
    }

    /**
     * @param $kind
     * @return string
     */
    public function getAnimPos($kind){
        if (($this->isAlive())) {
            switch ($kind) {
                case "wait":
                    return "stbwait";
                    break;
                case "damage":
                    return "damage";
                case "attack":
                    return "attack";
            }
        }
        return "dead";
    }
    /**
     * Associate raid team
     * @param ServerRaidTeam $raidTeam
     */
    public function setRaidTeam(ServerRaidTeam $raidTeam){
        $this->raidTeam = $raidTeam;
    }

    public function getRaidTeam(): ServerRaidTeam{
        return $this->raidTeam;
    }

    /**
     * @return ServerRaidAbility[]
     */
    public function getAbilitySkillList(){
        return $this->aSkillList;
    }

    public function canChargeAttack(){
        return ($this->getCurrentAttr('chargeBar') >= 100);
    }
    /**
     * For more random effect
     * @return array
     * @throws \Exception
     */
    public function getRandomDamagePos(){
        $randInt = random_int(-10,10);

        return [$this->position[0] +$randInt, $this->position[1] + $randInt];
    }

    public function onSuccessOneAttack(){

    }

    public function looseCurrentAttr($name, $value){
        $this->setCurrentAttr($name, GameHelper::clamp($this->getCurrentAttr($name) - $value, 0, $this->getMaxAttr($name)));
    }

    public function gainCurrentAttr($name, $value){
        $this->setCurrentAttr($name, GameHelper::clamp($this->getCurrentAttr($name) + $value, 0, $this->getMaxAttr($name)));
    }

    public function setCurrentAttr($name, $value){
        $this->currentAttribute[$name] = $value;
        // Handler
        if ($name == "chargeBar" && $this->charSlug != "Boss"){
            if ($value >= 100){
                $this->defaultWait = "ability";
            }
            else{
                $this->defaultWait = "stbwait";
            }
        }
    }

    public function getCurrentAttr($name){
        return $this->currentAttribute[$name];
    }

    public function getAttrPercent($name){
        return round($this->getCurrentAttr($name) / $this->getMaxAttr($name), 2) * 100;
    }

    public function getMaxAttr($name){
        return $this->maxAttribute[$name];
    }

    public function countdownTurn($countdown=1){
        foreach ($this->getAbilitySkillList() as $skill){
            $skill->countdownTurn();
        }
    }

    public function execSkill($index){
        $this->getAbilitySkillList()[$index]->execute();
    }

    public function execAttackNormal(ServerRaidCharacter $targetChar){
        // To store several step of damage for animation
        $damageStepResult = [];
        //
        $max_count = 3;
        if ($this->charSlug == "Boss"){
            $max_count = 1;
        }

        // Rand triple
        $tipleAttack = random_int(1, $max_count);

        // Apply damage for each attack
        for ($i = 0; $i < $tipleAttack; $i++){
            // To override
            $this->onSuccessOneAttack();
            // Damage formula by attack attempt for more random
            $damage = $this->getCurrentAttr('baseDamage');
            // Gain charbar
            $this->gainCurrentAttr('chargeBar', 10);
            // Random supplement , more strong by triple attack
            $supp = random_int(0, 1000) * ($i+1);
            // Store damage apply result
            $damageStepResult[$i] =$targetChar->looseHP($damage + $supp);
        }

        return $damageStepResult;
    }

    public function setOugiReady($active){
        $this->defaultWait = ($active) ? 'stbwait' : 'ability';
    }

    public function execOugiAttack(ServerRaidCharacter $targetChar){
        // To store several step of damage for animation
        $damageStepResult = [];
        // Apply damage for each attack
        // Damage formula by attack attempt for more random
        $damage = $this->getCurrentAttr('baseDamage') * 10;
        // Random supplement , more strong by triple attack
        $supp = random_int(0, 10000);
        // Store damage apply result
        $damageStepResult[0] = $targetChar->looseHP($damage + $supp);

        // Consume charge bar
        $this->looseCurrentAttr('chargeBar', 100);

        return $damageStepResult;
    }

    /**
     * @param $damage
     * @return array Damage Step result
     */
    public function looseHP($damage){
        $remainHp = GameHelper::clamp($this->getCurrentAttr('hp') - $damage, 0, 99999999999);
        $this->setCurrentAttr('hp', $remainHp);

        // If die
        if ($remainHp <1){
            // notify to the team you are dead
            $this->isAlive = false;
            $this->getRaidTeam()->onCharacterDie($this);
        }

        //
        return [
            'die' => ($remainHp < 1),
            'damage' => $damage,
            'remainHP' => $this->getCurrentAttr('hp'),
            'remainPercent' => round((($remainHp / $this->getMaxAttr('hp')) * 100), 0)
        ];
    }
}