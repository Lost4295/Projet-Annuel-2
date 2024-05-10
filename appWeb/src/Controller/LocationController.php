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
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LocationController extends AbstractController
{
    #[Route("/appartements", name: "appartements")]
    public function createPage(EntityManagerInterface $em)
    {
        $apparts = $em->getRepository(Appartement::class)->findAll();
        return $this->render('appartements/locations.html.twig', [
            'apparts' => $apparts
        ]);
    }


    #[Route("/appartements/{id}", name: "appart_detail", requirements: ['id' => '\d+'])]
    public function show($id, EntityManagerInterface $em, Request $request)
    {
        $appart = $em->getRepository(Appartement::class)->find($id);
        $passedlocs = $em->getRepository(Location::class)->findPassedLocations($appart->getId());
        $canComm = false;
        $commentaires = [];

        if ($passedlocs) {
            $canComm = true;
            $commentaires = $em->getRepository(Commentaire::class)->findComments("appartement", $appart->getId());
            $formComm = $this->createForm(CommentaireType::class, null, [
                'action' => $this->generateUrl('commentaire_create'),
                'method' => 'POST',
            ]);
        }
        $locs = $em->getRepository(Location::class)->findBy(["id" => $appart->getId()]);
        foreach ($locs as $loc) {
            $startDate = $loc->getDateDebut();
            $endDate = $loc->getDateFin();
            $dates[] = $startDate->format('Y-m-d');
            $dates[] = $endDate->format('Y-m-d');
            $interval = $startDate->diff($endDate);
            $numDays = $interval->days;
            for ($i = 1; $i < $numDays; $i++) {
                $date = $startDate->add(new DateInterval('P1D'))->format('Y-m-d');
                $dates[] = $date;
            }
        }
        $form = $this->createForm(LocationFirstType::class, null, [
            'action' => $this->generateUrl('appart_confirm'),
            'method' => 'POST',
        ]);
        dump($appart->getImages());
        $form->handleRequest($request);
        return $this->render('appartements/appart_detail.html.twig', [
            'appart' => $appart,
            'form' => $form,
            'dates' => $dates?? null,
            'canComm' => $canComm,
            'commentaires' => $commentaires,
            'formComm' => $formComm ?? null,
            'type' => Commentaire::APPART
        ]);
    }

    #[Route("/locations/confirm", name: "appart_confirm")]
    #[IsGranted("ROLE_USER")]
    public function confirm(Request $request, EntityManagerInterface $em)
    {
        
        $secondForm = $this->createForm(ConfirmLocationType::class, null, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stripe')
        ]);
        $firstForm = $this->createForm(LocationFirstType::class, null, [
            'method' => 'POST',
        ]);
        $firstForm->handleRequest($request);
        $id = $firstForm->get('appart')->getData();
        if (!$id) {
            $this->addFlash("danger", "noaccess");
            return $this->redirectToRoute('locations');
        }

        $locs = $em->getRepository(Location::class)->findBy(["id" => $id]);


        $dates = explode("-", $firstForm->get('date')->getData());
        $dates = array_map(function ($date) {
            return new \DateTime($date);
        }, $dates);
        if ($dates[0] == $dates[1]) {
            $this->addFlash("danger", "samedate");
            return $this->redirectToRoute('appart_detail', ['id' => $id]);
        }
        if ($firstForm->isSubmitted() && !$firstForm->isValid()) {
            return $this->redirectToRoute('appart_detail', ['id' => $id]);
        }
        $appart = $em->getRepository(Appartement::class)->find($id);

        return $this->render('appartements/confirm_appart.html.twig', [
            'firstForm' => $firstForm,
            'appart' => $appart,
            'secondForm' => $secondForm,
            'dates' => $dates
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
        return $this->render('appartements/create_appart.html.twig');
    }

    #[Route("/location/{id}", name: "location_info", requirements: ["id" => "\d+"])]
    #[IsGranted("ROLE_VOYAGEUR")]
    public function locationInfo($id, EntityManagerInterface $em)
    {
        $uid = $this->getUser();
        $loc = $em->getRepository(Location::class)->find($id);
        if (!$loc || $uid !== $loc->getLocataire()) {
            $this->addFlash("danger", "noaccess");
            return $this->redirectToRoute('profile');
        }
        return $this->render("location/location_detail.html.twig", ['location' => $loc]);
    }
}
