<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Entity\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiFacticeController extends AbstractController
{
    /**
     * @Route("/api/obsolete/genres", name="app_api_genres_factice")
     */
    public function index(): JsonResponse
    {
        $genre = new Genre();
        $genre->setName("Mon joli titre");

        return $this->json($genre);
    }
}
