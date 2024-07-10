<?php

// src/Controller/ExampleController.php

namespace App\Controller;

// On importe la classe AbstractController de Symfony, qui est une classe mère de tous les contrôleurs
use App\Entity\Devis;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Si vous avez besoin d'utiliser des services de Symfony ou même les notres, vous pouvez les importer ici avec des use


class ExampleController extends AbstractController
{
    public function __construct(private HttpClientInterface $client)
    {
    }
    #[Route('/example', name: 'example')]
    public function index(EntityManagerInterface $em, PdfService $pdf): Response
    {
// Ici, c'est l'endroit où on va mettre le code de notre page. 
// On va retourner une réponse, qui est le rendu de notre page twig.
// On va lui passer des variables, qui seront utilisées dans le fichier twig.
// On va lui passer le nom du fichier twig à utiliser, qui sera dans le dossier templates de notre projet.
// On va lui passer un tableau associatif, qui contiendra les variables à passer à notre fichier twig.
        
        // $response = $this->client->request('GET', 'https://api.unsplash.com/photos/random?client_id='.$_ENV['UNSPLASH_ACCESS_KEY'].'&query=person');
        // $content = $response->getContent();
        // $url = json_decode($content)->urls->regular;
        
        // $response = $this->client->request('GET', $url);
        // $content = $response->getContent();
        // file_put_contents(__DIR__.'/../../public/images/person.jpg', $content);
        // return $this->render('example.html.twig', [
        //     'name' => 'ExampleController',
        //     // le nom de la variable en twig, et sa valeur après
        //     'bool'=> true,
        //     'tab'=> ['a','b','c'],
        //     'obj'=> ['name'=>'toto','toto'=>30],
        // ]);
        $dev = $em->getRepository(Devis::class)->find(104);

        $res= $pdf->createDevisPdf($dev);

        return $this->render('example.html.twig', [
            'res' => $res,
        ]);
    }

}