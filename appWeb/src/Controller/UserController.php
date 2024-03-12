<?php

//src/Controller/UserController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{

    #[Route("/profile", name: "profile")]
    #[IsGranted("ROLE_USER")]
    public function profile()
    {
        return $this->render('profile.html.twig', ['message' => 'Hello World!']);
    }

}
