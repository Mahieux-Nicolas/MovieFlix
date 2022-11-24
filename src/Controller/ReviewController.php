<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\MovieRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    /**
     * @Route("/movie/{id<\d+>}/review", name="review_add")
     */
    public function add(int $id, MovieRepository $movieRepository, Request $request, ReviewRepository $reviewRepository): Response
    {
     // on récupère le film
        $movie = $movieRepository->find($id);

        // on génère le form
        $newReview = new Review();
        $form = $this->createForm(ReviewType::class, $newReview);

        // ajouter Request sinon on a pas d'info venant de notre utilisateur
        $form->handleRequest($request);

        // si le form est envoyé et valide
        if ($form->isSubmitted() && $form->isValid())
        {
            // ont fait l'association
            $newReview->setMovie($movie);
            // persist + flush
            $reviewRepository->add($newReview, true);

            //redirection
            return $this->redirectToRoute('movie_read', ['Slug' => $movie->getSlug()]);
        }

        return $this->render('review/add.html.twig', [
            'movie' => $movie,
            // on renvoie à twig la vue du formulaire
            'formulaire' => $form->createView()
        ]);
    }
}
