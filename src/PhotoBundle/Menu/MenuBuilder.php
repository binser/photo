<?php

namespace PhotoBundle\Menu;

use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilder
{
    private $factory;
    private $em;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('ГЛАВНАЯ', array('route' => 'photo_homepage'));
        $menu->addChild('ОБО МНЕ', array('route' => 'photo_about'));
        $menu->addChild('ПОРТФОЛИО', array());
        $albums = $this->em->getRepository('PhotoBundle:Album')->getAllActualAlbums();

        return $menu;
    }
}