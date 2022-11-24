<?php

namespace App\Controller\Backoffice;

use App\Entity\Movie;
use App\Form\Movie1Type;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Services\MySlugger;
use App\Services\OmdbApi;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// pour le @isGranted()
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * 
 * @Route("/backoffice/movie")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/", name="backoffice_movie_index", methods={"GET"})
     * 
     */
    //@isGranted("ROLE_LIST")
    public function index(MovieRepository $movieRepository): Response
    {
        //je vérifie si j'ai ROLE_LIST
        // génère une erreur 403
   
        $this->denyAccessUnlessGranted('ROLE_LIST');


        return $this->render('backoffice/movie/index.html.twig', [
            'movies' => $movieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="backoffice_movie_new", methods={"GET", "POST"})
     * @isGranted("ROLE_ADD")
     */
    public function new(Request $request, MovieRepository $movieRepository, OmdbApi $omdbApi, MySlugger $mySlugger): Response
    {
 

        $movie = new Movie();

        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            //  aller chercher le poster suivant le nom du film
            $poster = $omdbApi->fetchPoster($movie->getTitle());
            $movie->setPoster($poster);

            $slug = $mySlugger->slug($movie->getTitle());
            $movie->setSlug($slug);


            $movieRepository->add($movie, true);

            return $this->redirectToRoute('backoffice_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="backoffice_movie_show", methods={"GET"})
     */
    public function show(Movie $movie = null): Response
    {

        // gestion d'erreur
        if (!$movie) {throw $this->createNotFoundException('No movie found');}


        return $this->render('backoffice/movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/{title<\w+>}", name="backoffice_movie_show_title", methods={"GET"})
     */
    public function showByTitle(Movie $movie = null): Response
    {
   
        // gestion d'erreur
        if (!$movie) {throw $this->createNotFoundException('No movie found');}

        return $this->render('backoffice/movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backoffice_movie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Movie $movie = null, MovieRepository $movieRepository, MySlugger $mySlugger): Response
    {
        // gestion d'erreur
        if (!$movie) {throw $this->createNotFoundException('No movie found');}


        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()){
           
                $movie->setUpdatedAt(new DateTime());

  
                $newSlug = $mySlugger->slug($movie->getTitle());
                $movie->setSlug($newSlug);

                $movieRepository->add($movie, true);

                
                $this->addFlash('success', 'le film a bien été modifié');

                return $this->redirectToRoute('backoffice_movie_index', [], Response::HTTP_SEE_OTHER);
            }
           // flash message
            $this->addFlash('danger', 'le film n\'a pas été modifié');
        }

        return $this->renderForm('backoffice/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backoffice_movie_delete", methods={"POST"})
     */
    public function delete(Request $request, Movie $movie, MovieRepository $movieRepository): Response
    {
      //pour que la suppression soit effective on appelle le token CSRF, on le compare, s'il est pareil , on supprime l'élément.
        if ($this->isCsrfTokenValid(
            'delete'.$movie->getId(),
             $request->request->get('_token'))) {
                
            $movieRepository->remove($movie, true);
        }

        return $this->redirectToRoute('backoffice_movie_index', [], Response::HTTP_SEE_OTHER);
    }
}
