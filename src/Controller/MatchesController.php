<?php

namespace App\Controller;

use App\Entity\Matches;
use App\Entity\Team;
use App\Form\MatchesType;
use App\Repository\MatchesRepository;
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
    private EntityManagerInterface $entityManager;
    public function __construct(HomeMenu $menuPage, EntityManagerInterface $entityManager)
    {
        $this->menuPage = $menuPage;
        $this->entityManager = $entityManager;

    }
    public function deleteAllMatches(): void
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeStatement('DELETE FROM matches');
        $connection->executeStatement($platform->getTruncateTableSQL('matches', true));
    }
    private function dateModifier(DateTimeInterface $dateTime): DateTimeInterface
    {
        $dayStart = 9; // 9 AM
        $dayEnd = 23; // Midnight (24:00)

        $dateTime=$dateTime->modify('+2 hours');

        if ($dateTime->format('H') >= $dayEnd) {
            $dateTime=$dateTime->modify('+1 day');
            $dateTime=$dateTime->setTime($dayStart, 0, 0);
        }

        return $dateTime;
    }

    #[Route('/', name: 'app_matches_index', methods: ['GET'])]
    public function index(MatchesRepository $matchesRepository,EntityManagerInterface $entityManager): Response
    {

        $menu = $this->menuPage->createMenu();
        $matches=$matchesRepository->findAll();
        $teams=$entityManager->getRepository(Team::class)->findAll();
        $existingMatches = $matchesRepository->findAll();
        $nr=$entityManager->getRepository(Matches::class)->count([]);

        //Verific daca am mai generat deja echipele cand dau refresh
        $existingTeams = [];
        foreach ($existingMatches as $existingMatch) {
            $existingTeams[$existingMatch->getTeam1()->getId()][$existingMatch->getTeam2()->getId()] = true;
            $existingTeams[$existingMatch->getTeam2()->getId()][$existingMatch->getTeam1()->getId()] = true;
        }
        //Generez toate meciurile si le salvez in baza de date
        foreach ($teams as $team1)
            foreach ($teams as $team2)
                if($team1->getName()!=$team2->getName() && !isset($existingTeams[$team1->getId()][$team2->getId()]))
                {
                    $match=new Matches();
                    $match->setTeam1($team1);
                    $match->setTeam2($team2);
                   // $match->setDate(01.01.2001);
                    $match->setScore1(-1);
                    $match->setScore2(-1);
                    $entityManager->persist($match);

                }
        $entityManager->flush();

        //Le dau cate o data random
        $rndm=rand(0,$nr-1);
        if(!empty($matches) &&$matches[0]->getDate()==null){
            if($rndm>$nr/2)
                $m=(2*$rndm-$nr)/2;
            elseif($rndm<$nr/2)
                $m=($nr-2*$rndm)/2;

            $today=new \DateTime();
            $today->setTime(7, 0, 0);
            $this->dateModifier($today);
            for($i=0;$i<$rndm;$i++)
                if ($rndm+$i<$nr){
                    $matches[$i]->setDate(clone $today);
                    $this->dateModifier($today);
                    $matches[$rndm+$i]->setDate(clone $today);
                    $this->dateModifier($today);
                }
            if ($rndm<$nr/2){
                for($i=0;$i<$m;$i++)
                {
                    $matches[2*$rndm+$i]->setDate( clone $today);
                    $this->dateModifier($today);
                    $matches[2*$rndm+$m+$i]->setDate(clone $today);
                    $this->dateModifier($today);

                }}
            elseif ($rndm>$nr/2)
                for ($i=0;$i<$m;$i++)
                {
                    $matches[$nr-$rndm+$i]->setDate(clone $today);
                    $this->dateModifier($today);
                    $matches[$nr-$rndm+$m+$i]->setDate(clone $today);
                    $this->dateModifier($today);

                }

            $entityManager->flush();
        }
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
    public function edit(Request $request, Matches $match, MatchesRepository $matchesRepository): Response
    {
        $form = $this->createForm(MatchesType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $matchesRepository->save($match, true);

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
