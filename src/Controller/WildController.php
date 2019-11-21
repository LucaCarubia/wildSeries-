<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wild", name="wild_")
 */
class WildController extends AbstractController
{

    /**
     * @return Response
     * @Route("/index", name="index")
     */
    public function index(): Response
    {
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Séries',
        ]);
    }

    /**
     * @Route("/show/{slug}", requirements={"slug"="[a-z0-9-]+"}, defaults={"slug": ""}, name="show")
     * @param string $slug
     * @return Response
     */
    public function show(string $slug): Response
    {
        $replace = str_replace("-", " ", $slug);
        $result = ucwords($replace);

        return $this->render('wild/show.html.twig', [
            'result' => $result,
        ]);
    }
}