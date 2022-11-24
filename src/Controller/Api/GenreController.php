<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class GenreController extends ApiController
{
    /**
     * @Route("/api/genres", name="app_api_genre", methods={"GET"})
     */
    public function browse(GenreRepository $genreRepository): JsonResponse
    {
        // * gestion des roles et erreurs
        if (!$this->isGranted("ROLE_ERREUR"))
        {
            return $this->json(
                "tu n'as pas le droit",
                Response::HTTP_FORBIDDEN
            );
        }

        return $this->json(
            $genreRepository->findAll(),
            // le code HTTP 200
            Response::HTTP_OK,
            // entètes HTTP,[] par défaut, pas de modif
            [],
            // on mets les groupes de sérialization
            [
                "groups" => 
                [
                    "genre_browse"
                ]
            ]
        );
    }

     /**
     * Return a JSON response of one genre by his id
     *
     * @Route("/api/genres/{id<\d+>}", name="app_api_genres_read", methods={"GET"})
     * 
     * @param integer $id
     * @param GenreRepository $genreRepository
     * 
     * @return void
     */
    public function read(int $id, GenreRepository $genreRepository) 
    {
        $genre = $genreRepository->find($id);

        if ($genre === null) {
            return $this->json404(["erreur" => "Le genre n'a pas été trouvé"]);
        }

        return $this->json200(
            ["genre" => $genre],
            ["genre_read"]
        );
    }

    /**
     * ajout de genre
     * 
     * @Route("/api/genres", name="app_api_genre_add", methods={"POST"})
     */
    public function add(
        Request $request, 
        SerializerInterface $serializerInterface,
        GenreRepository $genreRepository,
        ValidatorInterface $validatorInterface)
    {
       
        $content = $request->getContent();
       
        // gestion des erreurs json 
        try {
            $newGenre = $serializerInterface->deserialize($content, Genre::class, 'json');
        } catch(Exception $e) // si pas possible
        {
            // on retourne une erreur
            return $this->json("Le JSON est mal formé", Response::HTTP_BAD_REQUEST);
        }
        
        
        //  validation des données via validatorInterface
    
        $errors = $validatorInterface->validate($newGenre);

        if (count($errors) > 0) {
            
            $errorsString = (string) $errors;
    
            return $this->json(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }


        // Tajout en BDD
        $genreRepository->add($newGenre, true);
        
        // retourner l'info du succès
        return $this->json(
            $newGenre,
            Response::HTTP_CREATED,
            [
                // redirection
                "Location" => $this->generateUrl("app_api_genres_read", ["id" => $newGenre->getId()])
            ],
        
            [
                "groups" => 
                [
                    "genre_read"
                ]
            ]
        );

    }

    /**
     * mise à jour d'un genre
     * 
     * @Route("/api/genres/{id<\d+>}", name="app_api_genre_edit", methods={"PUT", "PATCH"})
     *
     * @param ?Genre $genre
     * @param Request $request
     * @param SerializerInterface $serializerInterface
     * @param EntityManagerInterface $entityManagerInterface
     */
    public function edit(?Genre $genre, Request $request, SerializerInterface $serializerInterface, EntityManagerInterface $entityManagerInterface)
    {
        // Getsion du paramConverter
        if ($genre === null) {return $this->json404("Pas de genre pour cet ID");}
        
        // récupération du contenu JSON
        $jsonContent = $request->getContent();


        $serializerInterface->deserialize(
            $jsonContent,
            Genre::class,
            'json',
            // on précise l'objet à mettre à jour 
            [AbstractNormalizer::OBJECT_TO_POPULATE => $genre]
        );

     
        $entityManagerInterface->flush();

     
        return $this->json(
            $genre,
            Response::HTTP_PARTIAL_CONTENT,
            [
             
                "Location" => $this->generateUrl("app_api_genres_read", ["id" => $genre->getId()])
            ],
     
            [
                "groups" => 
                [
                    "genre_read"
                ]
            ]
        );
    }
}
