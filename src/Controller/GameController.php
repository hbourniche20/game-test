<?php

namespace App\Controller;

use App\Entity\RaidGame;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route("/game", name="game")
     */
    public function index(): Response
    {
        return $this->render('game/index.html.twig', [
            'controller_name' => 'GameController',
        ]);
    }

    /**
     * @IsGranted("ROLE_PLAYER")
     * @Route("/game/raid/{id}", name="game_raid")
     */
    public function raid($id): Response
    {
        // get the raid
        $raid = $this->getDoctrine()->getRepository(RaidGame::class)->find($id);
        $raidChar = $this->getUserRaidChar($this->getUser(), $raid);

        dump($raid->getRaidTeamByKind("player")->getCharacterList()[0]->getCharPlayer()->getClasse()->getLibData());
        dump($raid->getRaidTeamByKind("player")->getCharacterList()[0]->getCharPlayer()->getClasse()->getAbilityList()[0]);
        dump($raid->getRaidQuest());

        return $this->render('game/raid.html.twig', compact('raid', 'raidChar'));
    }

    public function getUserRaidChar($user, $raid){
        $foundedRaidCharUser = null;
        foreach ($raid->getRaidTeamByKind("player")->getCharacterList() as $raidChar){
            if ($raidChar->getCharPlayer()->getUser()->getUsername() === $user->getUsername()){
                $foundedRaidCharUser = $raidChar;
            }
        }
        return $foundedRaidCharUser;
    }
}
