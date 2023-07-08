<?php

namespace App\Controller;
use App\Controller\HomeMenu;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePage20Controller extends AbstractController
{
    #[Route('/home/page20', name: 'app_home_page20')]
    public function index(HomeMenu $menuPage): Response
    {
        $menu = $menuPage->createMenu();
        return $this->render('home_page20/index.html.twig', [
            //'controller_name' => 'HomePage20Controller',
            'menu' => $menu,
        ]);
    }
}
