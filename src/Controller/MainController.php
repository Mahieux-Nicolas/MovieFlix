<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\CastingRepository;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use App\Repository\ReviewRepository;
use App\Services\OmdbApi;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="default")
     * @Route("/movie", name="movie_browse")
     */
    public function browse(MovieRepository $movieRepository, GenreRepository $genreRepository): Response
    {

        $allMovies = $movieRepository->findAllOrderByTitle();
        $allGenre = $genreRepository->findAll();

        return $this->render('main/browse.html.twig', [
            "allMovies" => $allMovies,
            "allGenre" =>$allGenre
        ]);
    }

    /**
     * Affiche les détails d'un film
     *
     * Ancienne version sans slug : Route("/movie/{id<\d+>}",name="movie_read")
     * @Route("/movie/{Slug<[a-zA-Z0-9_-]+>}", name="movie_read")
     * 
     * @return Response
     */
    public function read($Slug, MovieRepository $movieRepository, CastingRepository $castingRepository, ReviewRepository $reviewRepository): Response
    {

        $movie = $movieRepository->findOneBy(
            // critères de sélection
            [
                "slug" => $Slug
            ]
        );

        if ($movie === null) {
            throw $this->createNotFoundException("Pas de film avec ce nom");
        }

        $allCastingFromMovie = $castingRepository->findBy(
            // critères de sélection
            [
                "movie" => $movie->getId()
            ],
            // order by
            [
                'creditOrder' => 'ASC'
                // OU 'role' => 'ASC'
            ]
        );



        //  récupérer les reviews
        $allReviews = $reviewRepository->findBy(
            // critères de sélection
            [
                "movie" => $movie->getId()
            ],
            // order by 
            [
                'rating' => 'DESC'
            ],
            // limit
            3
        );

        return $this->render('main/read.html.twig', [
            "movie" => $movie,
            "castings" => $allCastingFromMovie,
            'allReviews' => $allReviews
        ]);
    }



    /**
     * Modification d'un film
     *
     * @Route("/movie/{id}/edit",name="movie_edit")
     * 
     * @param integer $id
     * @param MovieRepository $movieRepository pour le find()
     * @param EntityManagerInterface $entityManagerInterface pour le flush()
     * @return Response
     */
    public function edit(int $id, MovieRepository $movieRepository, EntityManagerInterface $entityManagerInterface, GenreRepository $genreRepository): Response
    {
        // récupérer le film via le movie repository
        $movieToUpdate = $movieRepository->find($id);

        // mise à jour
        $dateDuJour = new DateTime();

        $movieToUpdate->setRating(9);

        // ajouter une genre aléatoire

        $allGenre = $genreRepository->findAll();

        $randNumber = rand(0, count($allGenre) - 1);
        $randGenre = $allGenre[$randNumber];
        $movieToUpdate->addGenre($randGenre);

        // on envoie directement via l'entitymanagerInterface
        $entityManagerInterface->flush();
        // redirection
        return $this->redirectToRoute('movie_read', ['id' => $id]);
    }

    /**
     * Création de film
     *
     * @Route("/movie/add",name="movie_add")
     * 
     * @return Response
     */
    public function add(MovieRepository $movieRepository): Response
    {

        // exemple
        $newMovie = new Movie();
        $newMovie->setTitle('filmexemple');
        $newMovie->setDuration('1800');
        $newMovie->setType("film");

        // persist + flush
        $movieRepository->add($newMovie, true);

        // une redirection vers page d'accueil
        return $this->redirectToRoute('movie_browse');
    }

    /**
     * supprimer un film
     *
     * @Route("/movie/{id}/delete",name="movie_delete")
     * 
     * @param integer $id
     * @return Response
     * @param MovieRepository $movieRepository
     */
    public function delete(int $id, MovieRepository $movieRepository): Response
    {
        $movieToDelete = $movieRepository->find($id);

        $movieRepository->remove($movieToDelete, true);

        return $this->redirectToRoute('movie_browse');
    }
}
