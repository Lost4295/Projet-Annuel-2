<?php

//src/Controller/DefaultController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\File;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FileType;

class DefaultController extends AbstractController
{

    
    #[Route("/index", name:"index")]
    public function index()
    {
        return $this->render('index.html.twig', ['message' => 'Hello World!']);
    }

    #[Route("/number", name:"number")]
    #[Route("/notifications", name:"notifications")]
    #[Route("/profile", name:"profile")]
    #[Route("/settings", name:"settings")]
    #[Route("/friends", name:"friends")]
    #[Route("/dashboard", name:"dashboard")]
    #[Route("/results", name:"results")]
    #[Route("/suggestions", name:"suggestions")]
    public function createPage(Request $request)
    {
        $routeName = $request->attributes->get("_route");

        if ($routeName === "number") {
            $number = random_int(0, 100);
        } else {
            $number = $routeName;
        }

        return $this->render('dashboard.html.twig', [
            'text' => $number,
        ]);
    }
    
    
    #[Route("/create/text", name:"new_text")]
    

    public function createText(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $text = new File();
        $form = $this->createForm(FileType::class, $text);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($text);
            $entityManager->flush();
            $this->addFlash('success', 'Texte créé avec succès');
            return $this->redirectToRoute('all_texts');
        }
        return $this->render('createtxt.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("alltexts", name:"all_texts")]
    

    public function allTexts(EntityManagerInterface $em)
    {
        $textes = $em->getRepository(File::class)->findAll();
        return $this->render('alltxt.html.twig', [
            'textes' => $textes,
        ]);
    }

    #[Route("text/{id}",name:"texte_show")]

    public function showText($id, EntityManagerInterface $em)
    {
        $texte = $em->getRepository(File::class)->find($id);
        return $this->render('showtxt.html.twig', [
            'texte' => $texte,
        ]);
    }
}
