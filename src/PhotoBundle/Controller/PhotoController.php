<?php

namespace PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PhotoController extends Controller
{
    public function indexAction()
    {
        return $this->render('PhotoBundle:Pages:index.html.twig', ['albums' => []]);
    }

    public function aboutAction()
    {
        return $this->render('PhotoBundle:Pages:index.html.twig', ['albums' => []]);
    }

    public function albumsAction($albumURL)
    {
        $albumURLs = $this->getDoctrine()
            ->getRepository('PhotoBundle:Album')
            ->getAllAlbumURLs();
        if(!in_array($albumURL, $albumURLs)) {
            throw new NotFoundHttpException("Не корректный адрес страницы");
        }

        $album = $this->getDoctrine()
            ->getRepository('PhotoBundle:Album')
            ->getAlbumByURL($albumURL);
        $photos = $album->getPhotos();

        return $this->render('PhotoBundle:Pages:index.html.twig', ['albums' => []]);
    }

    public function priceAction($pricePage)
    {
        switch ($pricePage) {
            case 'svadba':
                break;
            case 'progulka':
                break;
            case 'studija':
                break;
            case 'love_story':
                break;
            case 'vyezdnaja_semka':
                break;
            default:
                throw new NotFoundHttpException("Не корректный адрес страницы");
        }
        return $this->render('PhotoBundle:Pages:index.html.twig', ['albums' => []]);
    }

    public function blogAction()
    {
        return $this->render('PhotoBundle:Pages:index.html.twig', ['albums' => []]);
    }

    public function contactsAction()
    {
        return $this->render('PhotoBundle:Pages:index.html.twig', ['albums' => []]);
    }
}
