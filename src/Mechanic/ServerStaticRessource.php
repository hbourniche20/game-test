<?php

namespace App\Mechanic;

class ServerStaticRessource
{

    public $scenarioTemplate;

    public function __construct(){
        $this->scenarioTemplate = [];
    }

    public function addScenarioTemplateBundle($data){
        foreach ($data as $scenarioData){
            $this->scenarioTemplate[$scenarioData['identifier']] = $scenarioData;
        }
    }
}