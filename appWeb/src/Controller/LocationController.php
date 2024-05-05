<?php

//src/Controller/LocationController.php

namespace App\Controller;

use App\Entity\Appartement;
use App\Entity\Commentaire;
use App\Entity\Location;
use App\Form\CommentaireType;
use App\Form\ConfirmLocationType;
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
    public function createPage(EntityManagerInterface $em)
    {
        $apparts = $em->getRepository(Appartement::class)->findAll();
        return $this->render('appartements/locations.html.twig', [
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
        return $this->render('appartements/appart_detail.html.twig', [
            'appart' => $appart,
            'form' => $form,
            'canComm' => $canComm,
            'commentaires' => $commentaires,
            'formComm' => $formComm ?? null,
            'type'=> Commentaire::APPART
        ]);
    }

    #[Route("/locations/confirm", name: "appart_confirm")]
    #[IsGranted("ROLE_USER")]
    public function confirm(Request $request, EntityManagerInterface $em)
    {
        $firstForm = $this->createForm(LocationFirstType::class, null, [
            'method' => 'POST',
        ]);
        $firstForm->handleRequest($request);
        $id = $firstForm->get('appart')->getData();
        if ($firstForm->isSubmitted() && !$firstForm->isValid()) {
            return $this->redirectToRoute('appart_detail', ['id' => $id]);
        }
        $appart = $em->getRepository(Appartement::class)->find($id);
        $secondForm = $this->createForm(ConfirmLocationType::class, null, [
            'method' => 'POST',
        ]);
        return $this->render('appartements/confirm_appart.html.twig', [
            'firstForm' => $firstForm,
            'appart' => $appart,
            'secondForm'=> $secondForm
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
        return $this->render('location/create_location.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route("/create/commentaire", name: "commentaire_create")]
    #[IsGranted("ROLE_VOYAGEUR")]
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
    #[Route("/create_appart", name: "create_appart_bailleur")]
    #[IsGranted("ROLE_BAILLEUR")]
    public function createAppart(Request $request, EntityManagerInterface $em)
    {
        return $this->render('appart"ements/create_appart.html.twig');
    }
    
    #[Route("/location/{id}", name:"location_info", requirements: ["id"=> "\d+"])]
    #[IsGranted("ROLE_VOYAGEUR")]
    public function locationInfo($id, EntityManagerInterface $em){
        $uid =$this->getUser()->getUserIdentifier();
        $loc =$em->getRepository(Location::class)->find($id);
        if (!$loc){
            $this->addFlash("danger","noaccess" );
            return $this->redirectToRoute('profile');
        }

    }
}


