<?php

namespace App\Controller;
use Knp\Menu\FactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
class HomeMenu
{
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }
    public function createMenu()
    {
        $menu = $this->factory->createItem('root');


        $menu->addChild('Home', ['route' => 'app_home_page20']);
        $menu->addChild('Team', ['route' => 'app_team_index']);
        $menu->addChild('Matches', ['route' => 'app_matches_index']);
        $menu->addChild('Sponsors', ['route' => 'app_sponsors_index']);

        // Add more menu items as needed

        return $menu;
    }
}