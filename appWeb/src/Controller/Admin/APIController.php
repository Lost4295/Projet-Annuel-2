<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class APIController extends AbstractController
{

    #[Route("/apis", name: "api_state")]

    public function index(EntityManagerInterface $em): Response
    {
        $apis = $em->getRepository(Api::class)->findAll();
        return $this->render('admin/apis.html.twig', [
            'apis' => $apis,
        ]);
    }

    #[Route("/api/state", name: "api_state_update")]

    public function updateState(Request $request, EntityManagerInterface $em): Response
    {
        $id = $request->get('id');
        $api = $em->getRepository(Api::class)->find($id);
        if ($api) {
            $api->setIsdown(!$api->isDown());
            $em->persist($api);
            $em->flush();
        }
        return $this->json(['id'=> $id, 'isDown' => $api->isDown()]);
    }
}
