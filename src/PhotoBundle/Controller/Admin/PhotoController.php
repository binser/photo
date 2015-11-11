<?php

namespace PhotoBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PhotoController extends Controller
{
    public function indexAction($albumUrl) {
        $album = $this->getDoctrine()
            ->getRepository('PhotoBundle:Album')
            ->getAlbumByURL($albumUrl);
        if (!$album) {
            throw $this->createNotFoundException("Не найден альбом по URL: {$albumUrl}");
        }

        return $this->render('PhotoBundle:Pages/Admin/Photo:index.html.twig', array(
            'activeAlbumId' => $album->getId(),
            'photos' => $album->getPhotos()
        ));
    }
}