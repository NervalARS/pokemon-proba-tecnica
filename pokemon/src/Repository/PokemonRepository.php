<?php

namespace App\Repository;

use App\Entity\Pokemon;
use App\Entity\Votes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpClient\HttpClient;
/**
 *
 * @method Pokemon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pokemon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pokemon[]    findAll()
 * @method Pokemon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PokemonRepository extends ServiceEntityRepository
{
    private $baseUrl;
    public function __construct()
    {
        $this->baseUrl = $_ENV['BASE_URL'];
    }

    public function findAll() {

        $client = HttpClient::create();
        // executem la peticio cURL
        $crudeResponse = $client->request('GET', $this->baseUrl . '/pokemon');

        $response = json_decode($crudeResponse->getContent());

        $arrayPokemons = array();
        foreach ($response->results as $row) {
            $crudeResponse = $client->request('GET', $this->baseUrl . '/pokemon/' . $row->name);
            $jsonPokemon = json_decode($crudeResponse->getContent());
            $pokemon = new Pokemon();
            if($jsonPokemon){
                $pokemon->setId($jsonPokemon->id);
                $pokemon->setName($jsonPokemon->name);

                if(count($jsonPokemon->types) == 1){
                    $pokemon->setType(
                        array(
                            0 => $jsonPokemon->types[0]->type->name,
                        )
                    );
                }elseif(count($jsonPokemon->types) == 2){
                    $pokemon->setType(
                        array(
                            0 => $jsonPokemon->types[0]->type->name,
                            1 => $jsonPokemon->types[1]->type->name, 
                        )
                    );
                }
                $pokemon->setSprite($jsonPokemon->sprites->front_default);
            }
            array_push($arrayPokemons, $pokemon);
        }
        return $arrayPokemons;
    }

    public function findByName($name) {

        $client = HttpClient::create();
        // executem la peticio cURL
        $crudeResponse = $client->request('GET', $this->baseUrl . '/pokemon/' . $name);

        $jsonPokemon = json_decode($crudeResponse->getContent());
        $pokemon = new Pokemon();
       // foreach ($resposta as $row) {
            if($jsonPokemon){
                $pokemon->setId($jsonPokemon->id);
                $pokemon->setName($jsonPokemon->name);
                if(count($jsonPokemon->types) == 1){
                    $pokemon->setType(
                        array(
                            0 => $jsonPokemon->types[0]->type->name,
                        )
                    );
                }elseif(count($jsonPokemon->types) == 2){
                    $pokemon->setType(
                        array(
                            0 => $jsonPokemon->types[0]->type->name,
                            1 => $jsonPokemon->types[1]->type->name, 
                        )
                    );
                }
                $pokemon->setSprite($jsonPokemon->sprites->front_default);

                $crudeResponse = $client->request('GET', $this->baseUrl . '/pokemon-species/' . $name);
                $description = json_decode($crudeResponse->getContent());
                $pokemon->setDescription($description->flavor_text_entries[0]->flavor_text);
            }
        //}
        return $pokemon;
    }
}
