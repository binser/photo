<?php

namespace PhotoBundle\Menu;

use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use PhotoBundle\Entity\Album;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     * @param EntityManager $em
     */
    public function __construct(FactoryInterface $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('ГЛАВНАЯ', array('route' => 'photo_homepage', 'attributes' => array('class' => 'firstLevel')));
        $menu->addChild('ОБО МНЕ', array('route' => 'photo_about', 'attributes' => array('class' => 'firstLevel')));

        $menu->addChild('ПОРТФОЛИО', array('attributes' => array('class' => 'firstLevel')));
        $albums = $this->em->getRepository('PhotoBundle:Album')->getAllActualAlbums();
        /** @var Album $album */
        foreach ($albums as $album) {
            $caption = $album->getCaption();
            $menu['ПОРТФОЛИО']->addChild(mb_strtoupper($caption, 'UTF-8'), array(
                'route' => 'photo_albums',
                'routeParameters' => array('albumURL' => $album->getUrl()),
                'attributes' => array('class' => 'twoLevel')
            ));
        }

        $menu->addChild('УСЛУГИ И ЦЕНЫ', array('attributes' => array('class' => 'firstLevel')));
        $pricePages = [
            ['caption' => 'СВАДЬБА', 'url' => 'svadba'],
            ['caption' => 'ПРОГУЛКА', 'url' => 'progulka'],
            ['caption' => 'СТУДИЯ', 'url' => 'studija'],
            ['caption' => 'LOVE STORY', 'url' => 'love_story'],
            ['caption' => 'ВЫЕЗДНАЯ СЪЕМКА', 'url' => 'vyezdnaja_semka']
        ];
        foreach($pricePages as $pricePage) {
            $menu['УСЛУГИ И ЦЕНЫ']->addChild($pricePage['caption'], array(
                'route' => 'photo_price',
                'routeParameters' => array('pricePage' => $pricePage['url']),
                'attributes' => array('class' => 'twoLevel')
            ));
        }

        $menu->addChild('БЛОГ', array('route' => 'photo_blog', 'attributes' => array('class' => 'firstLevel')));
        $menu->addChild('КОНТАКТЫ', array('route' => 'photo_contacts', 'attributes' => array('class' => 'firstLevel')));

        return $menu;
    }

    public function createAdminMainMenu() {
        $menu = $this->factory->createItem('root');
        $menu->addChild('Редактировать блог', array('route' => 'admin_blog', 'attributes' => array('class' => 'itemButton')));
        $menu->addChild('Редактировать альбомы', array('route' => 'admin_albums', 'attributes' => array('class' => 'itemButton')));
        $menu->addChild('Редактировать фотографии', array('route' => 'admin_photos', 'attributes' => array('class' => 'itemButton')));

        return $menu;
    }

    public function createAdminAlbumsMenu() {
        $menu = $this->factory->createItem('root');
        $albums = $this->em->getRepository('PhotoBundle:Album')
            ->getAllActualAlbums();
        /** @var Album $album */
        foreach ($albums as $album) {
            $menu->addChild($album->getCaption(), array(
                'route' => 'admin_photos',
                'routeParameters' => array('albumUrl' => $album->getUrl()),
                'attributes' => array(
                    'class' => 'itemAlbum',
                    'albumId' => $album->getId()
                )
            ));
        }

        return $menu;
    }
}