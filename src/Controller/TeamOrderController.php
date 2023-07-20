<?php

namespace App\Controller;

use App\Entity\Matches;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamOrderController extends AbstractController
{

    #[Route('/order', name: 'app_team_order')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $teams=$entityManager->getRepository(Team::class)->findAll();
        $cmp = function ($a, $b) {
            if ($a->getPoint() == $b->getPoint()) {
                return ($a->getGoals() > $b->getGoals()) ? -1 : 1;
            }
            return ($a->getPoint() > $b->getPoint()) ? -1 : 1;
        };
        usort($teams,$cmp);

        $var=1;
        return $this->render('team_order/index.html.twig', [
            'controller_name' => 'TeamOrderController',
             'teams'=>$teams,
            'var'=>$var,

        ]);


    }


}
