<?php

namespace App\Controller;

use App\Entity\Sponsors;
use App\Entity\Team;
use App\Form\SponsorsType;
use App\Repository\SponsorsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sponsors')]
class SponsorsController extends AbstractController
{
    public function __construct(HomeMenu $menuPage,EntityManagerInterface $entityManager)
    {
        $this->menuPage = $menuPage;
        $this->entityManager = $entityManager;
    }
    public function falseSponsors(EntityManagerInterface $entityManager)
    {
        $faker=Factory::create();
        $team=$entityManager->getRepository(Team::class)->findAll();
        $noTeam=$entityManager->getRepository(Team::class)->count([]);
        for($i=1;$i<=7;$i++)
        {
            $sponsor=new Sponsors();
            $sponsor->setName($faker->company);
            $sponsor->setCommercialZone($faker->word);

            for($j=0;$j<=rand(1,$noTeam-1);$j++){
               $sponsor->addTeam($team[array_rand($team)]);
            }
            $entityManager->persist($sponsor);
        }
        $entityManager->flush();
    }

    #[Route('/deletesponsors', name: 'app_sponsors_delete_all', methods: ['POST'])]
    public function deleteAllSponsors(SponsorsRepository $sponsorsRepository)
    {
        $sponsors = $sponsorsRepository->findAll();

        foreach ($sponsors as $sponsor)
        {
            $sponsorsRepository->remove($sponsor,true);
        }

        return $this->redirectToRoute('app_sponsors_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/', name: 'app_sponsors_index', methods: ['GET','POST'])]
    public function index(SponsorsRepository $sponsorsRepository,EntityManagerInterface $entityManager): Response
    {   $menu = $this->menuPage->createMenu();

        return $this->render('sponsors/index.html.twig', [
            'sponsors' => $sponsorsRepository->findAll(),
            'menu' => $menu,
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
