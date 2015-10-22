<?php

namespace PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PhotoController extends Controller
{
    public function indexAction()
    {
        dump(111);
        return $this->render('PhotoBundle:pages:index.html.twig', ['albums' => []]);
    }

    public function aboutAction()
    {
        dump(111);
        return $this->render('PhotoBundle:pages:index.html.twig', ['albums' => []]);
    }

    public function albumsAction($albumURL)
    {
        dump(111);
        return $this->render('PhotoBundle:pages:index.html.twig', ['albums' => []]);
    }

    public function priceAction($pricePage)
    {
        dump(111);
        return $this->render('PhotoBundle:pages:index.html.twig', ['albums' => []]);
    }

    public function blogAction()
    {
        dump(111);
        return $this->render('PhotoBundle:pages:index.html.twig', ['albums' => []]);
    }

    public function contactsAction()
    {
        dump(111);
        return $this->render('PhotoBundle:pages:index.html.twig', ['albums' => []]);
    }
}
