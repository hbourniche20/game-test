<?php
namespace App\Websocket;

use App\Entity\RaidGame;
use App\Mechanic\Scenario\ScenarioHelper;
use App\Mechanic\ServerRaidGame;
use App\Mechanic\ServerRaidGameManager;
use App\Mechanic\ServerStaticRessource;
use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use MongoDB\Driver\Server;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class GameMessageHandler implements MessageComponentInterface
{

//    No Static
    protected $rootUrl = "http://10.101.0.254/game-test/public";
    protected $connections;
    protected $serializer;
    /**
     * Referenced by Raid Token
     * @var array
     */
    protected $raidGameList = [];

    public static $userIndexList = [];
    public static $abilityList = [];
    public static $userIndexSlugList = [];

    public function __construct()
    {
        $this->connections = new SplObjectStorage;

        // Serializer
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $normalizers = new ObjectNormalizer($classMetadataFactory);
        $this->serializer = new Serializer([$normalizers], $encoders);
        //

        $this->prepareFakeData();
    }

    public function checkRaidAuth($raidToken, $charSlug){
        if (array_key_exists($raidToken, $this->raidGameList)){

        }
        return false;
    }

    public function getRaidData($token) : ServerRaidGame{
        return $this->raidGameList[$token];
    }

    public function prepareFakeData(){
        // Init static
        ServerRaidGameManager::init();

        // get scenario template / game rest
        $json = file_get_contents($this->rootUrl .'/apig/raidGameRes/1');
        $scenarioTemplateData = json_decode($json, true);
        ServerRaidGameManager::$serverStaticRessource->addScenarioTemplateBundle($scenarioTemplateData);

        // get raid data
        $json = file_get_contents($this->rootUrl .'/apig/raidGame/1');
        $raidData = json_decode($json, true);
        // Parse server raid game
        $this->raidGameList[$raidData['token']] = new ServerRaidGame($raidData);

        // Fake index
        self::$userIndexSlugList[0] = 'le_fou_1';
        self::$userIndexSlugList[1] = 'Toto_Le_Grand';
        self::$userIndexSlugList[2] = 'hugo';
        self::$userIndexSlugList[3] = 'valentin_ternaire';

        // from str id
        self::$userIndexList = [];
        self::$userIndexList['1'] = 'le_fou_1';
        self::$userIndexList['2'] = 'Toto_Le_Grand';
        self::$userIndexList['3'] = 'hugo';
        self::$userIndexList['4'] = 'valentin_ternaire';
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // generate token to the connected
        $this->connections->attach($conn);
        $raidToken = "413f7e60c87e8f28e207fc59f859bcfe";
        parse_str($conn->httpRequest->getUri()->getQuery(),$queryarray);
        $userSlug = $queryarray['userSlug'];

        $raid = $this->raidGameList[$raidToken];
        // send message
        $response = ['action' => 'logged', 'userToken' => "", 'socketId' => $conn->resourceId];
        $response['scenarioListData'] = [];
        array_push($response['scenarioListData'], ServerRaidGameManager::getActualRaidStateScenario($raid, $userSlug));

        $jsonResponse = json_encode($response);

        $conn->send($jsonResponse);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Prepare response
        $response = [];
        $response['sendOnlyFormHimself'] = false;
        $scenarioListData = [];

        // Get data (decode json)
        $mshObj = json_decode($msg, JSON_OBJECT_AS_ARRAY);

        // Verification token
        // $userToken = $msg['userToken'];

        // checkToken($userToken) -> true si c'est bon ou false

        // -- La suite
        $name = $mshObj['name'];
        $userSlug = self::$userIndexList[''.$mshObj['userId']];
        $raidToken = $mshObj['raidToken'];

        $raidGame = $this->getRaidData($raidToken);

        // test
        if ($name == 'test'){
            // test normal attack scenario
//            $damageStepResult = $raidGame->getCharacter($userSlug)->execOugiAttack($raidGame->getBossChar());
//            array_push($scenarioListData, ScenarioHelper::buildPlayerOugiScenario($raidGame, $userSlug, 'Boss', $damageStepResult));
            // tes boss mortal D
            $damageStepResult = $raidGame->getCharacter("Boss")->applyMortalDamage($raidGame->getCharacter("le_fou_1"));
            array_push($scenarioListData, ScenarioHelper::buildBossMortalScenario($raidGame, "Boss", 'le_fou_1', $damageStepResult));
            // push scenario finally
            $response['scenarioListData'] = $scenarioListData;
        }
        // If ability ask
        if ($name == 'exec_ability'){
            $abIndex = intval($mshObj['index']);

            $response = ServerRaidGameManager::processPlayerSkill($this->getRaidData($raidToken), $userSlug, $abIndex);
         }
        // Action prepare
        if ($name == 'exec_turn_ready' || $name == 'exec_turn_ready_super_attack'){
            $response = $this->checkAttackAttemptChoice($raidGame, $response, $raidToken, $name, $userSlug);
        }

        // Check if sendonly exist else false by default
        if (!array_key_exists('sendOnlyFormHimself', $response)){
            $response['sendOnlyFormHimself'] = false;
        }
        // encode
        $responseJson = json_encode($response);

        foreach($this->connections as $connection)
        {
            // If return reponse only for himself
            if ($response['sendOnlyFormHimself']) {
                if ($connection === $from) {
                    $connection->send($responseJson);
                }
            }
            // Else send to all
            else{
                $connection->send($responseJson);
            }

        }
    }

    /**
     * Do the verification
     * @param $raidGame
     * @param $response
     * @param $raidToken
     * @param $name
     * @param $userSlug
     * @return array|mixed
     */
    public function checkAttackAttemptChoice($raidGame, $response, $raidToken, $name, $userSlug){
        // TERNAIRE POUR VALENTIN !
        $superAttack = ($name == 'exec_turn_ready_super_attack') ? true : false;

        // IMPORTANT ! CHECK SUPER CONDITION IF IS
        if ($superAttack){
            if (!$raidGame->getCharacter($userSlug)->canChargeAttack()){
                return ServerRaidGameManager::makeMessageTurnCallback("Votre barre de puissance n'est pas suffisante");
            }
        }

        // Push super attack or normal attack ready
        $allAreReady = $this->pushReadyPlayer($raidToken, $userSlug, $superAttack);

        // In case to wait need to only send this to himself
        $response['action'] = 'ready';
        $response['charSlug'] = $userSlug;

        // If all player are ready process to turn battle !
        if ($allAreReady){
            // And process turn battler (damage, anim, etc
            $response = $this->processTurnBattler($raidToken);
        }

        return $response;
    }

    /**
     * Push player ready prepared action
     * @param $charSlug
     * @param $superAttack
     * @return bool
     */
    public function pushReadyPlayer($raidToken, $charSlug, $superAttack) : bool{
        return ServerRaidGameManager::pushReadyPlayer($this->getRaidData($raidToken), $charSlug, $superAttack);
    }

    public function processTurnBattler($raidToken)
    {
        return ServerRaidGameManager::makeTurnBattle($this->getRaidData($raidToken));
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->connections->detach($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        $this->connections->detach($conn);
        $conn->close();
    }
}