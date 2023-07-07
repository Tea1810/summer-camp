<?php

namespace App\Controller;

use App\Entity\Sponsors;
use App\Form\SponsorsType;
use App\Repository\SponsorsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sponsors')]
class SponsorsController extends AbstractController
{
    #[Route('/', name: 'app_sponsors_index', methods: ['GET'])]
    public function index(SponsorsRepository $sponsorsRepository): Response
    {
        return $this->render('sponsors/index.html.twig', [
            'sponsors' => $sponsorsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sponsors_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SponsorsRepository $sponsorsRepository): Response
    {
        $sponsor = new Sponsors();
        $form = $this->createForm(SponsorsType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sponsorsRepository->save($sponsor, true);

            return $this->redirectToRoute('app_sponsors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sponsors/new.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sponsors_show', methods: ['GET'])]
    public function show(Sponsors $sponsor): Response
    {
        return $this->render('sponsors/show.html.twig', [
            'sponsor' => $sponsor,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sponsors_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sponsors $sponsor, SponsorsRepository $sponsorsRepository): Response
    {
        $form = $this->createForm(SponsorsType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sponsorsRepository->save($sponsor, true);

            return $this->redirectToRoute('app_sponsors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sponsors/edit.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sponsors_delete', methods: ['POST'])]
    public function delete(Request $request, Sponsors $sponsor, SponsorsRepository $sponsorsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sponsor->getId(), $request->request->get('_token'))) {
            $sponsorsRepository->remove($sponsor, true);
        }

        return $this->redirectToRoute('app_sponsors_index', [], Response::HTTP_SEE_OTHER);
    }
}
