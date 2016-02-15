<?php

namespace PhotoBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use PhotoBundle\Entity\Album;
use PhotoBundle\Entity\Photo;
use PhotoBundle\Entity\Post;

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
            Events::preRemove,
            Events::preUpdate
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
            /** @var Album[] $albums */
            $albums = $em->getRepository('PhotoBundle:Album')->findAll();
            foreach($albums as $album) {
                $currentSortIndex = $album->getSortIndex();
                $album->setSortIndex($currentSortIndex + 10);
            }
        } elseif($entity instanceof Photo) {
            $dateCreate = new \DateTime();
            $entity->setDateCreate($dateCreate);
        } elseif($entity instanceof Post) {
            $dateCreate = new \DateTime();
            $entity->setDateCreate($dateCreate);
            $entity->setDateUpdate($dateCreate);
            $entity->setEnabled(true);

            $this->savePostImages($entity);
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

    public function preUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if($entity instanceof Post) {
            $dateUpdate = new \DateTime();
            $entity->setDateUpdate($dateUpdate);

            $this->savePostImages($entity);
        }
    }

    private function savePostImages(Post &$post) {
        $text = $post->getText();
        preg_match_all('/src="(.*?)"/', $text, $m);
        if (isset($m[1]) && $m[1]) {
            $images = json_encode($m[1]);
            $mainImage = array_shift($m[1]);
            $post->setMainImage($mainImage);
            $post->setImages($images);
        } else {
            $post->setMainImage('');
            $post->setImages('');
        }
    }
}
