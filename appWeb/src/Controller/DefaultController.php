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
    
    public function number()
    {
        $number = random_int(0, 100);

        return $this->render('number.html.twig', [
            'number' => $number,
        ]);
    }
    #[Route("/notifications", name:"notifications")]
    
    public function notifications()
    {
        $notifications = "notifications";

        return $this->render('notifications.html.twig', [
            'notifications' => $notifications,
        ]);
    }
    #[Route("/profile", name:"profile")]
    
    public function profile()
    {
        $profile = "profile";

        return $this->render('profile.html.twig', [
            'profile' => $profile,
        ]);
    }
    #[Route("/settings", name:"settings")]
    
    public function settings()
    {
        $settings = "settings";

        return $this->render('settings.html.twig', [
            'settings' => $settings,
        ]);
    }
    #[Route("/friends", name:"friends")]
    
    public function friends()
    {
        $friends = "friends";

        return $this->render('friends.html.twig', [
            'friends' => $friends,
        ]);
    }

    #[Route("/dashboard", name:"dashboard")]
    

    public function dashboard()
    {
        $dashboard = "dashboard";

        return $this->render('dashboard.html.twig', [
            'dashboard' => $dashboard,
        ]);
    }

    #[Route("/results", name:"results")]
    
    public function results()
    {
        $results = "results";

        return $this->render('results.html.twig', [
            'results' => $results,
        ]);
    }

    #[Route("/suggestions", name:"suggestions")]
    
    public function suggestions()
    {
        $suggestions = "suggestions";

        return $this->render('suggestions.html.twig', [
            'suggestions' => $suggestions,
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
