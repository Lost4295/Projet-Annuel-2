<?php

// src/Controller/ExampleController.php

namespace App\Controller;

// On importe la classe AbstractController de Symfony, qui est une classe mère de tous les contrôleurs
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Si vous avez besoin d'utiliser des services de Symfony ou même les notres, vous pouvez les importer ici avec des use


class ExampleController extends AbstractController
{
    #[Route('/example', name: 'example')]
    public function index(): Response
    {
// Ici, c'est l'endroit où on va mettre le code de notre page. 
// On va retourner une réponse, qui est le rendu de notre page twig.
// On va lui passer des variables, qui seront utilisées dans le fichier twig.
        return $this->render('example.html.twig', [
            'name' => 'ExampleController',
            // le nom de la variable en twig, et sa valeur après
            'bool'=> true,
            'tab'=> ['a','b','c'],
            'obj'=> ['name'=>'toto','toto'=>30],
        ]);
    }
}