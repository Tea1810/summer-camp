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

#[Route('/matches')]
class MatchesController extends AbstractController
{
    public function __construct(HomeMenu $menuPage)
    {
        $this->menuPage = $menuPage;
    }
    #[Route('/', name: 'app_matches_index', methods: ['GET'])]
    public function index(MatchesRepository $matchesRepository): Response
    {

        $menu = $this->menuPage->createMenu();
        $matches=$matchesRepository->findAll();
        $cmp = function ($a, $b) {
            if ($a->getDate() == $b->getDate()) {
                return 0;
            }
            return ($a->getDate() > $b->getDate()) ? -1 : 1;
        };
        usort($matches,$cmp);
        return $this->render('matches/index.html.twig', [
            'match' => $matchesRepository->findAll(),
            'matches'=>$matches,
            'menu' =>$menu,

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
