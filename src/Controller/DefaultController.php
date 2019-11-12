<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @return Response
     * @Route("/", name="app_index")
     */
    public function index(): Response
    {
        $welcome = "Bienvenue !";

        return $this->render('default.html.twig', [
            'welcome' => $welcome,
        ]);
    }
}