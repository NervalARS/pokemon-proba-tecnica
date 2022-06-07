<?php

namespace App\Controller;

use App\Repository\PokemonRepository;
use App\Entity\Vote;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PokemonController extends AbstractController
{
    /**
     * @Route("/vote/{id}", name="pokemon_profile")
     */
    public function profile($id, Request $request)
    {
        $pokemonRepository = new PokemonRepository();
        $pokemon = $pokemonRepository->findByName($id);
        $votes = $this->getDoctrine()->getRepository(Vote::class)->findBy(['pokemon_id' => $pokemon->getId()]);
        $pokemon->setNumberOfVotes(count($votes));
        return $this->render('pokemon/profile.html.twig', [
            'pokemon' => $pokemon, 
        ]);
    }
}
