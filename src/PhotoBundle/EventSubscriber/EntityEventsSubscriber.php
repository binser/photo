<?php

namespace PhotoBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use PhotoBundle\Entity\Photo;

class EntityEventsSubscriber implements EventSubscriber
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $_container;

    function __construct($container)
    {
        $this->_container = $container;
    }

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
        } elseif($entity instanceof Photo) {
            $dateCreate = new \DateTime();
            $entity->setDateCreate($dateCreate);
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
        } elseif($entity instanceof Photo) {
            $name = $entity->getName();
            $pathToImageDirectory = $this->_container->get('request')->server->get('DOCUMENT_ROOT') . '/uploaded/images';
            $paths = array(
                $pathToImageDirectory . '/800x800/',
                $pathToImageDirectory . '/200x200/'
            );
            foreach($paths as $path) {
                $fullName = $path . $name;
                if (file_exists($fullName)) {
                    unlink($fullName);
                }
            }
        }
    }
}
