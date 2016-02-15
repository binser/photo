<?php

namespace PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PhotoController extends Controller
{
    public function indexAction()
    {
        $photos = $this->getDoctrine()->getRepository('PhotoBundle:Photo')->getLastPhotos();
        return $this->render('PhotoBundle:Pages:index.html.twig', array('photos' => $photos));
    }

    public function aboutAction()
    {
        return $this->render('PhotoBundle:Pages:about.html.twig');
    }

    public function albumsAction($albumURL)
    {
        $album = $this->getDoctrine()
            ->getRepository('PhotoBundle:Album')
            ->getAlbumByURL($albumURL);
        if(!$album) {
            throw new NotFoundHttpException("Не корректный адрес страницы");
        }
        /* TODO переписать под пагинатор */
        $photos = $album->getPhotos();

        return $this->render('PhotoBundle:Pages:album.html.twig', ['photos' => $photos->toArray()]);
    }

    public function priceAction($pricePage)
    {
        switch ($pricePage) {
            case 'svadba':
                return $this->render('PhotoBundle:Pages/Prices:svadba.html.twig');
                break;
            case 'progulka':
                return $this->render('PhotoBundle:Pages/Prices:progulka.html.twig');
                break;
            case 'studija':
                return $this->render('PhotoBundle:Pages/Prices:studija.html.twig');
                break;
            case 'love_story':
                return $this->render('PhotoBundle:Pages/Prices:love_story.html.twig');
                break;
            case 'vyezdnaja_semka':
                return $this->render('PhotoBundle:Pages/Prices:vyezdnaja_semka.html.twig');
                break;
            default:
                throw new NotFoundHttpException("Не корректный адрес страницы");
        }
    }

    public function blogAction($page)
    {
        $countPostsOnPage = $this->getParameter('posts_on_page');
        $start = $countPostsOnPage * ($page - 1);
        $posts = $this->getDoctrine()
            ->getRepository('PhotoBundle:Post')
            ->getPosts($start, $countPostsOnPage);

        return $this->render('PhotoBundle:Pages:blog.html.twig', array('posts' => $posts));
    }

    public function contactsAction()
    {
        return $this->render('PhotoBundle:Pages:contact.html.twig');
    }

    public function rentAction() {
        return $this->render('PhotoBundle:Pages:rent.html.twig');
    }

    public function articleAction($id) {
        $post = $this->getDoctrine()->getRepository('PhotoBundle:Post')->find($id);
        return $this->render('PhotoBundle:Pages:article.html.twig', array('post' => $post));
    }
}
