<?php

namespace App\Controller;

use App\Entity\Matches;
use App\Entity\Team;
use App\Form\MatchesType;
use App\Repository\MatchesRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeInterface;
use DateTime;




#[Route('/matches')]
class MatchesController extends AbstractController
{

    public function __construct(HomeMenu $menuPage, EntityManagerInterface $entityManager)
    {
        $this->menuPage = $menuPage;
        $this->entityManager = $entityManager;

    }
//    public function deleteAllMatches(): void
//    {
//        $connection = $this->entityManager->getConnection();
//        $platform = $connection->getDatabasePlatform();
//        $connection->executeStatement('DELETE FROM matches');
//        $connection->executeStatement($platform->getTruncateTableSQL('matches', true));
//    }
    private function dateModifier(DateTimeInterface $dateTime): DateTimeInterface
    {

        $dateTime=$dateTime->modify('+3 hours');

        if ($dateTime->format('H') >= 21) {
            $dateTime=$dateTime->modify('+1 day');
            $dateTime=$dateTime->setTime(9, 0, 0);
        }

        return $dateTime;
    }

    #[Route('/', name: 'app_matches_index', methods: ['GET','POST'])]
    public function index(MatchesRepository $matchesRepository,EntityManagerInterface $entityManager): Response
    {
        $menu = $this->menuPage->createMenu();
        $matches=$matchesRepository->findAll();
        $teams=$entityManager->getRepository(Team::class)->findAll();
        //Le sortez dupa data
        $cmp = function ($a, $b) {
            if ($a->getDate() == $b->getDate()) {
                return 0;
            }
            return ($a->getDate() < $b->getDate()) ? -1 : 1;
        };
        usort($matches,$cmp);
        //$this->deleteAllMatches();
        return $this->render('matches/index.html.twig', [
            'match' => $matchesRepository->findAll(),
            'matches'=>$matches,
            'menu' =>$menu,
            'teams'=>$teams,


        ]);

    }

    #[Route('/new', name: 'app_matches_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MatchesRepository $matchesRepository): Response
    {
        $match = new Matches();
        $form = $this->createForm(MatchesType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $matchesRepository->save($match, true);

            return $this->redirectToRoute('app_matches_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('matches/new.html.twig', [
            'match' => $match,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_matches_show', methods: ['GET'])]
    public function show(Matches $match): Response
    {
        return $this->render('matches/show.html.twig', [
            'match' => $match,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_matches_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Matches $match, MatchesRepository $matchesRepository,
                         EntityManagerInterface $entityManager, TeamRepository $teamRepository): Response
    {
        $form = $this->createForm(MatchesType::class, $match);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $matchesRepository->save($match, true);
            if($match->getScore1()>$match->getScore2())
            {
                $team1=$match->getTeam1();
                $team2=$match->getTeam2();
                $team1->setPoint($team1->getPoint()+3);
                $team1->setWins($team1->getWins()+1);
                $team2->setLosses(($team2)->getLosses()+1);
                $team1->setGoals($team1->getGoals()+$match->getScore1());
                $team2->setGoals($team2->getGoals()+$match->getScore2());
            }
            elseif($match->getScore1()<$match->getScore2())
            {
                $team1=$match->getTeam1();
                $team2=$match->getTeam2();
                $team2->setPoint($team2->getPoint()+3);
                $team2->setWins($team2->getWins()+1);
                $team1->setLosses(($team1)->getLosses()+1);
                $team1->setGoals($team1->getGoals()+$match->getScore1());
                $team2->setGoals($team2->getGoals()+$match->getScore2());
            }
            else {
                $team1=$match->getTeam1();
                $team2=$match->getTeam2();
                $team1->setPoint($team1->getPoint()+1);
                $team2->setPoint($team2->getPoint()+1);
                $team1->setGoals($team1->getGoals()+$match->getScore1());
                $team2->setGoals($team2->getGoals()+$match->getScore2());
            }
            $teamRepository->save($team1);
            $teamRepository->save($team2);
            $entityManager->flush();
            return $this->redirectToRoute('app_matches_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('matches/edit.html.twig', [
            'match' => $match,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_matches_delete', methods: ['POST'])]
    public function delete(Request $request, Matches $match, MatchesRepository $matchesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$match->getId(), $request->request->get('_token'))) {
            $matchesRepository->remove($match, true);
        }

        return $this->redirectToRoute('app_matches_index', [], Response::HTTP_SEE_OTHER);
    }


}
