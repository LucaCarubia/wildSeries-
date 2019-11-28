<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wild", name="wild_")
 */
class WildController extends AbstractController
{

    /**
     * @Route("/index", name="index")
     * @return Response
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        return $this->render('wild/index.html.twig', [
            'programs' => $programs,
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
}