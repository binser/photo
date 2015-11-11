<?php

namespace PhotoBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class Album implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::preRemove
        );
    }

    /**
     * Перед сохранением альбома выставление ему sortIndex в 10,
     * для того чтобы он отображался первым в списке,
     * и увеличение у остальных альбомов sortIndex на 10
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
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

    /**
     * Перед удаление альбома, удаляем все фотограффии, находящиеся в нем,
     * и уменьшаем у всех последующих альбомов sortIndex на 10
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof \PhotoBundle\Entity\Album) {
            $photos = $entity->getPhotos();
            foreach($photos as $photo) {
                $em->remove($photo);
            }

            $sortIndex = $entity->getSortIndex();
            $albums = $em->getRepository('PhotoBundle:Album')
                ->getAlbumsWithMoreSortIndex($sortIndex);
            foreach($albums as $album) {
                $currentSortIndex = $album->getSortIndex();
                $album->setSortIndex($currentSortIndex - 10);
            }
        }
    }
}
