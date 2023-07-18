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
        $match=$entityManager->getRepository(Matches::class)->findAll();
        $teams=$entityManager->getRepository(Team::class)->findAll();
        $cmp = function ($a, $b) {
            if ($a->getPoint() == $b->getPoint()) {
                return ($a->getGoals() > $b->getGoals()) ? -1 : 1;
            }
            return ($a->getPoint() > $b->getPoint()) ? -1 : 1;
        };
        usort($teams,$cmp);
       foreach ($match as $team)
       {
           $name=$team->getTeam1(); $name->setPoint(0);$name->setGoals(0);$name->setWins(0);$name->setLosses(0);
           $name=$team->getTeam2(); $name->setPoint(0);$name->setGoals(0);$name->setWins(0);$name->setLosses(0);

           $entityManager->flush();
       }
       foreach ($match as $team1) {
           if ($team1->getScore1() > $team1->getScore2() && $team1->getScore1()!=-1) {
               $name = $team1->getTeam1();$secondteam=$team1->getTeam2();
               $score=$name->getPoint() + 3; $name->setPoint($score);
               $name->setWins($name->getWins()+1);$secondteam->setLosses($secondteam->getLosses()+1);
               $name->setGoals($name->getGoals()+$team1->getScore1());
               $secondteam->setGoals($secondteam->getGoals()+$team1->getScore2());
           } elseif ($team1->getScore1() < $team1->getScore2() && $team1->getScore1()!=-1) {

               $secondteam = $team1->getTeam2(); $firstteam=$team1->getTeam1();
               $score=$secondteam->getPoint() + 3;$secondteam->setPoint($score);
               $secondteam->setWins($secondteam->getWins()+1);$firstteam->setLosses($firstteam->getLosses()+1);
               $firstteam->setGoals($firstteam->getGoals()+$team1->getScore1());
               $secondteam->setGoals($secondteam->getGoals()+$team1->getScore2());
           } elseif($team1->getScore1()!=-1) {
               $nameTeam1 = $team1->getTeam1();
               $score=$nameTeam1->getPoint() + 1;
               $nameTeam1->setPoint($score);

               $nameTeam2 = $team1->getTeam2();
               $score=$nameTeam2->getPoint()+1;
               $nameTeam2->setPoint($score);
               $nameTeam1->setGoals($nameTeam1->getGoals()+$team1->getScore1());
               $nameTeam2->setGoals($nameTeam2->getGoals()+$team1->getScore2());
           }
           $entityManager->flush();
       }
        $var=1;

        return $this->render('team_order/index.html.twig', [
            'controller_name' => 'TeamOrderController',
             'teams'=>$teams,
            'var'=>$var,

        ]);


    }
    public function generateTeams($noTeams,$teams)
    {

        $rndm=rand(0,$noTeams-1);


        if($rndm>$noTeams/2)
            $m=(2*$rndm-$noTeams)/2;
        elseif($rndm<$noTeams/2)
            $m=($noTeams-2*$rndm)/2;
        $generatedTeams=[];
        for($i=0;$i<$rndm;$i++)
            if ($rndm+$i<$noTeams){
                $generatedTeams[]=[$teams[$i], $teams[$rndm+$i]];
            }
        if ($rndm<$noTeams/2){
            for($i=0;$i<$m;$i++)
            {
                $generatedTeams[]=[$teams[2*$rndm+$i], $teams[2*$rndm+$m+$i]];

            }}
        elseif ($rndm>$noTeams/2)
            for ($i=0;$i<$m;$i++)
            {
                $generatedTeams[]=[$teams[$noTeams-$rndm+$i], $teams[$noTeams-$rndm+$m+$i]];

            }
            return $generatedTeams;
    }

}
