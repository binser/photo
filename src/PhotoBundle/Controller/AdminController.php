<?php

namespace PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction() {
        return $this->render('PhotoBundle:Pages/Admin:index.html.twig');
    }
}