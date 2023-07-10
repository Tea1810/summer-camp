<?php

namespace App\Controller;

use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamOrderController extends AbstractController
{
    #[Route('/order', name: 'app_team_order')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $noTeams= $entityManager->getRepository(Team::class)->count([]);
        $rndm=rand(0,$noTeams-1);
        $teams=$entityManager->getRepository(Team::class)->findAll();

        return $this->render('team_order/index.html.twig', [
            'controller_name' => 'TeamOrderController',
            'rndm'=>$rndm,
            'teams'=>$teams,
            'nr'=>$noTeams,
        ]);


    }

}
