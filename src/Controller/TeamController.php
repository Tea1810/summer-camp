<?php

namespace App\Controller;

use App\Entity\Matches;
use App\Entity\Player;
use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeInterface;
use DateTime;

#[Route('/team')]
class TeamController extends AbstractController
{
    private $menuPage;
    public function falsePlayers(EntityManagerInterface $entityManager):void
    {   $teams=$entityManager->getRepository(Team::class)->findAll();
        $faker=Factory::create();

        foreach($teams as $team)
        {   if(sizeof($team->getPlayers())<10){
            for($i=0;$i<4;$i++)
            {   $product= new Player();
                $product->setName($faker->name);
                $product->setAge($faker->numberBetween(16,40));
                $product->setRole("Midfielder");
                $product->setTeam($team);
                $product->setNationality($faker->country);
                $entityManager->persist($product);

            }
            for($i=0;$i<4;$i++) {
                $product = new Player();
                $product->setName($faker->name);
                $product->setAge($faker->numberBetween(16, 40));
                $product->setRole("Defender");
                $product->setTeam($team);
                $product->setNationality($faker->country);
                $entityManager->persist($product);
            }
                for($i=0;$i<2;$i++)
                {   $product= new Player();
                    $product->setName($faker->name);
                    $product->setAge($faker->numberBetween(16,40));
                    $product->setRole("Forward");
                    $product->setTeam($team);
                    $product->setNationality($faker->country);
                    $entityManager->persist($product);

                }
                $product= new Player();
                $product->setName($faker->name);
                $product->setAge($faker->numberBetween(16,40));
                $product->setRole("Goalkeeper");
                $product->setTeam($team);
                $product->setNationality($faker->country);
                $entityManager->persist($product);}
        }
        $entityManager->flush();
    }
    public function falseTeam(EntityManagerInterface $entityManager):void
    {   $teams=["FC Rapid","FC Hermannstadt","Universitatea Craiova","FCSB","FC Universitatea Cluj"];
        $faker=Factory::create();
        for($i=0;$i<5;$i++)
        {
            $team=new Team();
            $team->setName($teams[$i]);
            $team->setCoach($faker->name());
            $team->setCreationDate($faker->dateTime);
            $entityManager->persist($team);
        }
        $entityManager->flush();
    }
    public function __construct(HomeMenu $menuPage,EntityManagerInterface $entityManager)
    {
        $this->menuPage = $menuPage;
        $this->entityManager = $entityManager;
    }
    /*public function deleteAllMatches(): void
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeStatement('DELETE FROM team');
        $connection->executeStatement($platform->getTruncateTableSQL('team', true));
    }*/

    #[Route('/', name: 'app_team_index', methods: ['GET'])]
    public function index(TeamRepository $teamRepository,EntityManagerInterface $entityManager): Response
    {
        $menu = $this->menuPage->createMenu();
       // $this->falseTeam($entityManager);
        //$this->deleteAllMatches();
        return $this->render('team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
            'menu' => $menu,
        ]);
    }
    private function dateModifier(DateTimeInterface $dateTime): DateTimeInterface
    {

        $dateTime=$dateTime->modify('+3 hours');

        if ($dateTime->format('H') >= 21) {
            $dateTime=$dateTime->modify('+1 day');
            $dateTime=$dateTime->setTime(9, 0, 0);
        }

        return $dateTime;
    }
    #[Route('/new', name: 'app_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TeamRepository $teamRepository,EntityManagerInterface $entityManager): Response
    {
        $team = new Team();
        $teams=$teamRepository->findAll();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $teamRepository->save($team, true);
            foreach ($teams as $team1){
                    if($team1->getName()!=$team->getName())
                    {
                        $match=new Matches();
                        $match->setTeam1($team1);
                        $match->setTeam2($team);
                        // $match->setDate(01.01.2001);
                        $match->setScore1(-1);
                        $match->setScore2(-1);
                        $entityManager->persist($match);

                        $match=new Matches();
                        $match->setTeam1($team);
                        $match->setTeam2($team1);
                        // $match->setDate(01.01.2001);
                        $match->setScore1(-1);
                        $match->setScore2(-1);
                        $entityManager->persist($match);
                    }
            }
            $entityManager->flush();
            $matches=$entityManager->getRepository(Matches::class)->findAll();
            $unplayedMatches=[]; $tillThisDay=[];  $setMatches=[]; $unavailableHours=[];
            foreach ($matches as $match)
            {
                if($match->getScore1()==-1){
                    $unplayedMatches[]=$match;

                }
                else{
                    $daykey=$match->getDate()->format('d');
                    $tillThisDay[$daykey][$match->getTeam1()->getId()]=true;
                    $tillThisDay[$daykey][$match->getTeam2()->getId()]=true;
                    $setMatches[$match->getId()]=true;
                    $unavailableHours[]=$match->getDate();
                }
            }
            $nr=count($unplayedMatches);
            $day=new DateTime();
            $day->setTime(9, 0, 0);
            while(in_array($day,$unavailableHours)==true)
                $day=$this->dateModifier($day);
            $passedThroughTeams=0;

            while ($passedThroughTeams<$nr){
                foreach ($unplayedMatches as $match)
                {
                    $daykey=$day->format('d');
                    if(!isset($tillThisDay[$daykey][$match->getTeam1()->getId()]) and !isset($tillThisDay[$daykey][$match->getTeam2()->getId()]) && !isset($setMatches[$match->getId()]))
                    {
                        $match->setDate(clone $day);
                        $passedThroughTeams++;
                        $tillThisDay[$daykey][$match->getTeam1()->getId()]=true;
                        $tillThisDay[$daykey][$match->getTeam2()->getId()]=true;
                        $setMatches[$match->getId()]=true;
                        $this->dateModifier($day);

                    }

                }
                $day=$day->modify('+1 day');
                $day=$day->setTime(9, 0, 0);
            }$entityManager->flush();
            return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('team/new.html.twig', [
            'team' => $team,
            'form' => $form,

        ]);
    }

    #[Route('/{id}', name: 'app_team_show', methods: ['GET'])]
    public function show(Team $team,EntityManagerInterface $entityManager): Response
    {   $teams=$entityManager->getRepository(Team::class)->findAll();
       //  $this->deleteAllMatches();
        //$this->falsePlayers($entityManager);
        return $this->render('team/show.html.twig', [
            'team' => $team,
            'tem'=>$teams,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_team_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Team $team, TeamRepository $teamRepository): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teamRepository->save($team, true);

            return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_team_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team, TeamRepository $teamRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $teamRepository->remove($team, true);
        }

        return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
    }


}
