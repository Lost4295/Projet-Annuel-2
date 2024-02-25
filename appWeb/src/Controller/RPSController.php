<?php


//src/Controller/RPSController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\File;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FileType;

class RPSController extends AbstractController
{
    
    #[Route("/rps", name:"rps_index")]
     
    public function index()
    {
        return $this->render('rps/index.html.twig');
    }

    
    #[Route("/rps/game", name:"rps_game")]
        public function game()
    {
        $game = "game";
        return $this->render('rps/game.html.twig', [
            'game' => $game,
        ]);
    }
}