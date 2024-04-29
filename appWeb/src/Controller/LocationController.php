<?php

//src/Controller/LocationController.php

namespace App\Controller;

use App\Entity\Appartement;
use App\Entity\Commentaire;
use App\Entity\Location;
use App\Form\CommentaireType;
use App\Form\LocationFirstType;
use App\Form\LocationTestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LocationController extends AbstractController
{
    #[Route("/locations", name: "locations")]
    public function createPage(Request $request, EntityManagerInterface $em)
    {
        $routeName = $request->attributes->get("_route");

        if ($routeName === "number") {
            $number = random_int(0, 100);
        } else {
            $number = $routeName;
        }
        $apparts = $em->getRepository(Appartement::class)->findAll();
        return $this->render('locations.html.twig', [
            'text' => $number,
            'apparts' => $apparts
        ]);
    }

    #[Route("/locations/{id}", name: "appart_detail", requirements: ['id' => '\d+'])]
    public function show($id, EntityManagerInterface $em, Request $request)
    {
        $appart = $em->getRepository(Appartement::class)->find($id);
        $passedlocs = $em->getRepository(Location::class)->findPassedLocations($appart->getId());
        $canComm = false;
        $commentaires = [];

        if ($passedlocs) {
            $canComm = true;
            $commentaires = $em->getRepository(Commentaire::class)->findComments("appartement",$appart->getId());
            $formComm = $this->createForm(CommentaireType::class, null, [
                'action' => $this->generateUrl('commentaire_create'),
                'method' => 'POST',
            ]);
        }
        $location = new Location();
        $form = $this->createForm(LocationFirstType::class, null, [
            'action' => $this->generateUrl('appart_confirm'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);
        // if ($form->isSubmitted() && $form->isValid()) {
        //     return $this->redirectToRoute('appart_confirm');
        // }
        return $this->render('appart_detail.html.twig', [
            'appart' => $appart,
            'form' => $form,
            'canComm' => $canComm,
            'commentaires' => $commentaires,
            'formComm' => $formComm ?? null,
            'type'=> Commentaire::APPART
        ]);
    }

    #[Route("/locations/confirm", name: "appart_confirm")]
    public function confirm(Request $request)
    {
        $firstForm = $this->createForm(LocationFirstType::class, null, [
            'method' => 'POST',
        ]);
        $firstForm->handleRequest($request);
        if ($firstForm->isSubmitted() && !$firstForm->isValid()) {
            return $this->redirectToRoute('appart_detail', ['id' => 1]);
        }
        return $this->render('confirm_appart.html.twig', [
            'firstForm' => $firstForm
        ]);
    }

    #[Route("/create/location", name: "test_location_create")]
    #[IsGranted("ROLE_USER")]
    public function createLocation(Request $request, EntityManagerInterface $em)
    {
        $location = new Location();
        $form = $this->createForm(LocationTestType::class, $location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();
            return $this->redirectToRoute('profile');
        }
        return $this->render('create_location.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route("/create/commentaire", name: "commentaire_create")]
    #[IsGranted("ROLE_USER")]
    public function createCommentaire(Request $request, EntityManagerInterface $em)
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $commentaire->setDate(new \DateTime())
            ->setUser($user);
            $em->persist($commentaire);
            $em->flush();
            return $this->redirectToRoute('appart_detail', ['id' => $commentaire->getEntityId()]);
        }
    }
}


