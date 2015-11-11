<?php

namespace PhotoBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

class Album
{
    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var \PhotoBundle\Entity\Album $entity */
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof \PhotoBundle\Entity\Album) {
            $entity->setSortIndex(10);

            $albums = $em->getRepository('PhotoBundle:Album')->findAll();
            foreach($albums as $album) {
                $currentSortIndex = $album->getSortIndex();
                $album->setSortIndex($currentSortIndex + 10);
            }
        }
    }
}
