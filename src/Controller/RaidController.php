<?php

namespace App\Controller;

use App\Entity\RaidGame;
use App\Entity\ScenarioTemplate;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RaidController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ObjectRepository */
    private $objectRepository;

    private $serializer;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->objectRepository = $this->entityManager->getRepository(RaidGame::class);
        // json encoder
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new XmlEncoder(), new JsonEncoder()];

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];

        $normalizers = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        // $normalizers = new ObjectNormalizer($classMetadataFactory);

        $this->serializer = new Serializer([$normalizers], $encoders);
    }


    /**
     * @Route("/apig/raidGame/{id}", name="api_raid_game")
     */
    public function getRaidGame($id): Response
    {
        $raid = $this->getDoctrine()->getRepository(RaidGame::class)->find($id);

        $jsonContent = $this->serializer->serialize($raid, 'json');
        return new Response($jsonContent);
    }
    /**
     * @Route("/apig/raidGameRes/{id}", name="api_raid_game_res")
     */
    public function getRaidResGame(): Response
    {
        $scenarioTemplate = $this->getDoctrine()->getRepository(ScenarioTemplate::class)->findAll();

        $jsonContent = $this->serializer->serialize($scenarioTemplate, 'json');
        return new Response($jsonContent);
    }
}
