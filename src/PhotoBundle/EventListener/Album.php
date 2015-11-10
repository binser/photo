<?php

namespace PhotoBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

class Album
{
    private $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var \PhotoBundle\Entity\Album $entity */
        $entity = $args->getEntity();

        if ($entity instanceof \PhotoBundle\Entity\Album) {
            $entity->setSortIndex(10);
            $this->em->persist($entity);

            $albums = $this->em->getRepository('PhotoBundle:Album')->findAll();
            foreach($albums as $album) {
                $currentSortIndex = $album->getSortIndex();
                $album->setSortIndex($currentSortIndex + 10);
                $this->em->persist($album);
            }

            $this->em->flush();
        }
    }
}