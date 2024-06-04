<?php

//src/Controller/ServiceController.php

namespace App\Controller;

use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ServiceController extends AbstractController
{

    #[Route("/services", name: "services")]
    public function table(EntityManagerInterface $em)
    {

        $nettoyage = $em->getRepository(Service::class)->findBy(["type" => Service::NETTOYAGE]);
        $electricite = $em->getRepository(Service::class)->findBy(["type" => Service::ELECTRICITE]);
        $plomberie = $em->getRepository(Service::class)->findBy(["type" => Service::PLOMBERIE]);
        $peinture = $em->getRepository(Service::class)->findBy(["type" => Service::PEINTURE]);
        $bricolage = $em->getRepository(Service::class)->findBy(["type" => Service::BRICOLAGE]);
        $chauffeur = $em->getRepository(Service::class)->findBy(["type" => Service::CHAUFFEUR]);

        return $this->render('services/service.html.twig', [
            'nettoyage' => $nettoyage,
            'electricite' => $electricite,
            'plomberie' => $plomberie,
            'peinture' => $peinture,
            'bricolage' => $bricolage,
            'chauffeur' => $chauffeur
        ]);
    }

    #[Route("/service/{id}", name: "spec_service")]
    public function serv($id, EntityManagerInterface $em)
    {
        $service = $em->getRepository(Service::class)->find($id);
        return $this->render('services/specservice.html.twig', [
            'service' => $service,
        ]);
    }


    #[Route("/service/{id}/delete", name: "delete_service")]
    public function delete($id, EntityManagerInterface $em)
    {
        $service = $em->getRepository(Service::class)->find($id);
        $em->remove($service);
        $em->flush();
        return $this->redirectToRoute('services');
    }

    #[Route("/service/{id}/edit", name: "edit_service")]
    public function edit($id, EntityManagerInterface $em, Request $request)
    {
        $service = $em->getRepository(Service::class)->find($id);
        if ($request->isMethod('POST')) {
            $service->setTitre($request->request->get('titre'));
            $service->setDescription($request->request->get('description'));
            $service->setTarifs($request->request->get('tarifs'));
            $service->setType($request->request->get('type'));
            $em->persist($service);
            $em->flush();
            return $this->redirectToRoute('services');
        }
        return $this->render('services/editservice.html.twig', [
            'service' => $service,
        ]);
    }
}