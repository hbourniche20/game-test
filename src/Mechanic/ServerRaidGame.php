<?php

namespace App\Mechanic;

class ServerRaidGame {

    protected $data;
    public $playerActionPrepare;
    public $countReadyPlayer;
    public $characterList;
    protected $raidTeamList;

    public function __construct($data)
    {
        // Data
        $this->setData($data);
        // Default clear
        $this->clearTurn();
        // Create game character
        $this->characterList = [];
        $this->raidTeamList = [];

        foreach ($data['raidTeamList'] as $raidTeamData){
            // Create raid team
            $this->raidTeamList[$raidTeamData['kind']] = new ServerRaidTeam();

            // reset player index
            $playerIndex = 0;

            // For each player in the raid team data
            foreach ($raidTeamData['characterList'] as $characterData){
                $slug = "Boss";
                if ($characterData['charPlayer']['user'] !== null){
                    $slug = $characterData['charPlayer']['user']['slug'];
                }
                // instance depend of character kind
                if ($slug == "Boss"){
                    // Instance : Boss
                     $this->characterList[$slug] = new ServerRaidBossChar($characterData, $playerIndex);
                    // Add to Boss raid team
                    $this->getBossRaidTeam()->addCharacter($this->characterList[$slug]);
                }
                else{
                    // Instance : Player
                    $this->characterList[$slug] = new ServerRaidCharacter($characterData, $playerIndex);
                    // Add to Player raid team
                    $this->getPlayerRaidTeam()->addCharacter($this->characterList[$slug]);
                }

                // increment
                $playerIndex++;
            }
        }

    }

    public function clearTurn(){
        $this->playerActionPrepare = [];
        $this->countReadyPlayer = 0;
    }

    /**
     * @return mixed
     */
    public function getBossChar()
    {
        return $this->characterList["Boss"];
    }

    /**
     * @return ServerRaidTeam[]
     */
    public function getAllRaidTeam(): array {
        return $this->raidTeamList;
    }

    /**
     * @return mixed
     */
    public function getPlayerRaidTeam(): ServerRaidTeam
    {
        return $this->raidTeamList['player'];
    }

    /**
     * @return mixed
     */
    public function getBossRaidTeam(): ServerRaidTeam
    {
        return $this->raidTeamList['enemy'];
    }

    /**
     * @return ServerRaidCharacter[]
     */
    public function getAllCharacterList(): array
    {
        return $this->characterList;
    }

    /**
     * @return mixed
     */
    public function getCharacter($slug): ServerRaidCharacter
    {
        return $this->characterList[$slug];
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

}