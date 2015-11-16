<?php

namespace PhotoBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PhotoBundle\Entity\Album;

class LoadAlbumData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $svadba = new Album();
        $svadba->setCaption('Свадьба');
        $svadba->setEnabled();
        $svadba->setSortIndex(10);
        $svadba->setUrl('svadba');

        $semja = new Album();
        $semja->setCaption('Семья');
        $semja->setEnabled();
        $semja->setSortIndex(20);
        $semja->setUrl('semja');

        $ozhidanie = new Album();
        $ozhidanie->setCaption('Ожидание');
        $ozhidanie->setEnabled();
        $ozhidanie->setSortIndex(30);
        $ozhidanie->setUrl('ozhidanie');

        $progulka = new Album();
        $progulka->setCaption('Прогулка');
        $progulka->setEnabled();
        $progulka->setSortIndex(40);
        $progulka->setUrl('progulka');

        $proekty = new Album();
        $proekty->setCaption('Проекты');
        $proekty->setEnabled();
        $proekty->setSortIndex(50);
        $proekty->setUrl('proekty');

        $studija = new Album();
        $studija->setCaption('Студия');
        $studija->setEnabled();
        $studija->setSortIndex(60);
        $studija->setUrl('studija');

        $reportazh = new Album();
        $reportazh->setCaption('Репортаж');
        $reportazh->setEnabled();
        $reportazh->setSortIndex(70);
        $reportazh->setUrl('reportazh');

        $em->persist($svadba);
        $em->persist($semja);
        $em->persist($ozhidanie);
        $em->persist($progulka);
        $em->persist($proekty);
        $em->persist($studija);
        $em->persist($reportazh);
        $em->flush();

        $this->addReference('album-progulka', $progulka);
    }

    public function getOrder()
    {
        return 1;
    }

}