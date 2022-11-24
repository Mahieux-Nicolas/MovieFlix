<?php

namespace App\Controller\Api;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MovieController extends ApiController
{
    /**
     * @Route("/api/movies", name="app_api_movies_browse", methods={"GET"})
     */
    public function browse(MovieRepository $movieRepository): JsonResponse
    {
       
        return $this->json(
            // les données
            $movieRepository->findAll(),
            // le code HTTP 200
            Response::HTTP_OK,
            // les entètes HTTP, on n'a pas de besoin de les modifier, [] par défaut
            [],
            //  groupes de serialisation
            [
                "groups" => 
                [
                    "movie_browse"
                ]
            ]
        );
    }

    /**
     * @Route("/api/movies/{id<\d+>}", name="app_api_movies_read", methods={"GET"})
     */
    public function read(Movie $movie = null)
    {
        if ($movie === null){

            return $this->json(
                // les données ne sont pas obligatoirement des Entités
                [
                    "erreur" => "le film n'a pas été trouvé"
                ],
                // le code HTTP 404
                Response::HTTP_NOT_FOUND,
        
            );
        }

        return $this->json(
         
            $movie,
          
            Response::HTTP_OK,
        
            [],
            [
                "groups" => 
                [
                    "movie_browse"
                ]
            ]
        );
    }

    /**
     * Ajout de film
     * 
     * @Route("/api/movies", name="app_api_movies_add", methods={"POST"})
     *
     * @param Request $request
     * @param SerializerInterface $serializerInterface
     * @param ValidatorInterface $validatorInterface
     * @param EntityManagerInterface $entityManagerInterface
     */
    public function add(
        Request $request,
        SerializerInterface $serializerInterface,
        ValidatorInterface $validatorInterface,
        EntityManagerInterface $entityManagerInterface
    )
    {
        
        // infos venant de l'utilisateur : Request
        $jsonContent = $request->getContent();
        // 2. deserialize le JSON de la requete : SerializerInterface
        try {
            
            $newMovie = $serializerInterface->deserialize($jsonContent, Movie::class, 'json');
        } catch(Exception $e)
        {
            return $this->json(
                "JSON mal formé",
                Response::HTTP_BAD_REQUEST
            );
        }
        
  
        $errors = $validatorInterface->validate($newMovie);
        if (count($errors) > 0 ){
            return $this->json(
                $newMovie,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [],
                ["groups" => ["movie_error_add"]]
            );
        }
     
        $entityManagerInterface->persist($newMovie);
        $entityManagerInterface->flush();

  
        return $this->json(
            $newMovie,
            Response::HTTP_CREATED,
            [
               
                "Location" => $this->generateUrl("app_api_movies_read", ["id" => $newMovie->getId()])
            ],
            ["groups" => ["movie_read"]]
        );
    }
}
