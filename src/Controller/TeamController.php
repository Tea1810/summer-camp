<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/team')]
class TeamController extends AbstractController
{
    private $menuPage;
    private EntityManagerInterface $entityManager;
    public function falsePlayers(EntityManagerInterface $entityManager)
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
    public function falseTeam(EntityManagerInterface $entityManager)
    {
        $faker=Factory::create();
        for($i=1;$i<=10;$i++)
        {
            $team=new Team();
            $team->setName($faker->country);
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
    public function deleteAllMatches(): void
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeStatement('DELETE FROM player');
        $connection->executeStatement($platform->getTruncateTableSQL('player', true));
    }

    #[Route('/', name: 'app_team_index', methods: ['GET'])]
    public function index(TeamRepository $teamRepository): Response
    {
        $menu = $this->menuPage->createMenu();

        return $this->render('team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
            'menu' => $menu,
        ]);
    }
    #[Route('/new', name: 'app_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TeamRepository $teamRepository): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $teamRepository->save($team, true);

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
        $this->falsePlayers($entityManager);
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
