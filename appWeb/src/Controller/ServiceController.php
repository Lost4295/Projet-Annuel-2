<?php

//src/Controller/ServiceController.php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\User;
use App\EventSubscriber\ModerationSubscriber;
use App\Form\ServiceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route("/service/{id}", name: "spec_service", requirements: ['id' => '\d+'])]
    public function serv($id, EntityManagerInterface $em)
    {
        $service = $em->getRepository(Service::class)->find($id);
        return $this->render('services/specservice.html.twig', [
            'service' => $service,
        ]);
    }


    #[Route("/service/delete/{id}", name: "service_delete", requirements: ['id' => '\d+'])]
    #[IsGranted("ROLE_PRESTA")]
    public function delete($id, EntityManagerInterface $em)
    {
        $service = $em->getRepository(Service::class)->find($id);
        $user = $this->getUser();
        if (!$user || $user->getId() !== $service->getPrestataire()->getResponsable()->getId()) {
            throw $this->createAccessDeniedException();
        }
        $em->remove($service);
        $em->flush();
        return $this->redirectToRoute('services');
    }

    #[Route("/service/{id}/edit", name: "service_modify")]
    #[IsGranted("ROLE_PRESTA")]
    public function edit($id, EntityManagerInterface $em, Request $request)
    {
        $service = $em->getRepository(Service::class)->find($id);
        $user = $this->getUser();
        if (!$user || $user->getId() !== $service->getPrestataire()->getResponsable()->getId()) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(ServiceType::class, $service);
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder("modify_service", ServiceType::class, $service, array(
            'auto_initialize'=>false // it's important!!!
        ));
        $builder->addEventSubscriber(new ModerationSubscriber($em, $request));
        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($service);
            $em->flush();
            return $this->redirectToRoute('services');
        }
        return $this->render('services/modservice.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/service/add", name: "create_service")]
    #[IsGranted("ROLE_PRESTA")]
    public function add(EntityManagerInterface $em, Request $request)
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder("modify_profile", ServiceType::class, $service, array(
            'auto_initialize'=>false // it's important!!!
        ));
        $builder->addEventSubscriber(new ModerationSubscriber($em, $request));
        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->setPrestataire($this->getUser()->getPrestataire());
            $em->persist($service);
            $em->flush();
            return $this->redirectToRoute('services');
        }
        return $this->render('services/modservice.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);

    }
}
