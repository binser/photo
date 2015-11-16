<?php

namespace PhotoBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PhotoBundle\Entity\Photo;

class LoadPhotoData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $photo1 = new Photo();
        $photo1->setEnabled();
        $photo1->setName('cd07ea547b1e72ebce020a277a1c8849.jpg');
        $photo1->setAlbum($this->getReference('album-progulka'));

        $photo2 = new Photo();
        $photo2->setEnabled();
        $photo2->setName('650a66c7210b756f9a57e648d2b6d6cd.jpg');
        $photo2->setAlbum($this->getReference('album-progulka'));

        $photo3 = new Photo();
        $photo3->setEnabled();
        $photo3->setName('18290ebcfcb7867260b4445595a7bf8a.jpg');
        $photo3->setAlbum($this->getReference('album-progulka'));

        $photo4 = new Photo();
        $photo4->setEnabled();
        $photo4->setName('5ac4829e232eb024d9a9c154ad321fc6.jpg');
        $photo4->setAlbum($this->getReference('album-progulka'));

        $em->persist($photo1);
        $em->persist($photo2);
        $em->persist($photo3);
        $em->persist($photo4);
        $em->flush();
    }

    public function getOrder()
    {
        return 2;
    }

}