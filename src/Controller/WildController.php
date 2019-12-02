<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CategoryType;
use App\Form\ProgramSearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wild", name="wild_")
 */
class WildController extends AbstractController
{

    /**
     * @Route("/index", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        $form = $this->createForm(
            ProgramSearchType::class,
            null,
            ['method' => Request::METHOD_GET]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            // $data contains $_POST data
            // TODO : Faire une recherche dans la BDD avec les infos de $data…
        }

//        $category = new Category();
//        $form = $this->createForm(CategoryType::class, $category);


        return $this->render('wild/index.html.twig', [
            'programs' => $programs,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show")
     * @param string $slug The slugger
     * @return Response
     */
    public function show(?string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug' => $slug,
        ]);
    }

    /**
     * @Route("/category/{categoryName}", name="show_category")
     * @param string $categoryName
     * @return Response
     */
    public function showByCategory(string $categoryName): Response
    {
        $repositoryCategory = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repositoryCategory->findOneBy(['name' => $categoryName]);

        if (!$categories) {
            throw $this->createNotFoundException('this title was not found');
        }
        $idCategory = $categories->getId();

        $repositoryProgram = $this->getDoctrine()->getRepository(Program::class);

        $programs = $repositoryProgram->findBy(
            ['category' => $idCategory],
            ['id' => 'desc'],
            3,
            0
        );

        return $this->render('wild/category.html.twig', [
            'programs' => $programs,
        ]);
    }

    /**
     * @Route("/program/{programName<^[a-z0-9-]+$>}", defaults={"programName" = null}, name="show_program")
     * @param string $programName
     * @return Response
     */
    public function showByProgram(?string $programName): Response
    {
        if (!$programName) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $programName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($programName)), "-")
        );

        $repositoryProgram = $this->getDoctrine()->getRepository(Program::class);

        $program = $repositoryProgram->findOneBy(
            ['title' => mb_strtolower($programName)
            ]);

        $seasonProgram = $program->getSeasons();

        return $this->render('wild/program.html.twig', [
            'program' => $program,
            'seasons' => $seasonProgram,
        ]);
    }

    /**
     * @Route("/season/{id}", name="show_season")
     * @param int $id
     * @return Response
     */
    public function showBySeason(int $id): Response
    {
        $repositorySeason = $this->getDoctrine()->getRepository(Season::class);
        $season = $repositorySeason->find($id);
        $program = $season->getProgramId();
        $episode = $season->getEpisodes();

        return $this->render('wild/season.html.twig', [
            'program' => $program,
            'episode' => $episode,
            'season' => $season,
        ]);
    }

    /**
     * @Route("/episode/{id}", name="show_episode")
     * @param Episode $episode
     * @return Response
     */
    public function showEpisode(Episode $episode): Response
    {
        $season = $episode->getSeasonId();
        $program = $season->getProgramId();

        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'season' => $season,
            'program' => $program,
        ]);
    }

    /**
     * @Route("/actor/{name}", name="show_actor")
     * @param Actor $actor
     * @return Response
     */
    public function showActor(Actor $actor): Response
    {
        return $this->render('wild/showActor.html.twig', [
            'actor' => $actor,
        ]);
    }
}