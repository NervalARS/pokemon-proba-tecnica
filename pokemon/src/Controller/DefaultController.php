<?php

namespace App\Controller;

use App\Repository\PokemonRepository;
use App\Entity\Vote;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $pokemonRepository = new PokemonRepository();
        $pokemons = $pokemonRepository->findAll();
        foreach($pokemons as $pokemon){
            $votes = $this->getDoctrine()->
            getRepository(Vote::class)->findBy(['pokemon_id' => $pokemon->getId()]);
            $pokemon->setNumberOfVotes(count($votes));
        }
        return $this->render('default/home.html.twig', [
            'controller_name' => 'DefaultController', 
            'pokemons' => $pokemons, 
        ]);
    }

    /**
     * @Route("/vote/{id<\d+>}", name="vote_add")
     */
    public function VoteAdd($id, Request $request)
    {
        $newVote = new Vote();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $votes = $this->getDoctrine()->
        getRepository(Vote::class)->findBy(['user' => $user, 'pokemon_id' => $id]);
        if(count($votes) == 0){
            $newVote->setPokemonId($id);
            $newVote->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newVote);
            $entityManager->flush();
        }
        return $this->redirectToRoute('home');
    }
}
