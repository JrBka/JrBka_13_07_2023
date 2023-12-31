<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * This function displays the homepage
     * @Route("/", name="homepage")
     */
    public function indexAction(): Response
    {
        return $this->render('default/index.html.twig');
    }
}