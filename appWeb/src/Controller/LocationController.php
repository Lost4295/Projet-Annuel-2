<?php

//src/Controller/LocationController.php

namespace App\Controller;

use App\Entity\Appartement;
use App\Entity\Commentaire;
use App\Entity\Location;
use App\EventSubscriber\ModerationSubscriber;
use App\Form\AppartementType;
use App\Form\CommentaireType;
use App\Form\ConfirmLocationType;
use App\Form\LocationFirstType;
use App\Form\LocationTestType;
use App\Service\AppartementService;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LocationController extends AbstractController
{
    #[Route("/appartements", name: "appartements")]
    public function createPage(EntityManagerInterface $em, AppartementService $as)
    {
        $apparts = $em->getRepository(Appartement::class)->findAll();
        foreach ($apparts as $appart) {
            $as->updateAppart($appart->getId());
        }
        return $this->render('appartements/locations.html.twig', [
            'apparts' => $apparts
        ]);
    }


    #[Route("/appartements/{id}", name: "appart_detail", requirements: ['id' => '\d+'])]
    public function show($id, EntityManagerInterface $em, Request $request, AppartementService $as)
    {
        $appart = $em->getRepository(Appartement::class)->find($id);
        if (!$appart) {
            $this->addFlash("danger", "noaccess");
            return $this->redirectToRoute('appartements');
        }
        $as->updateAppart($id);
        $passedlocs = $em->getRepository(Location::class)->findPassedLocations($appart->getId());
        $canComm = false;
        $commentaires = [];
        
        $commentaires = $em->getRepository(Commentaire::class)->findComments("appartement", $appart->getId());
        if ($passedlocs) {
            $canComm = true;
            $formComm = $this->createForm(CommentaireType::class, null, [
                'action' => $this->generateUrl('commentaire_create'),
                'method' => 'POST',
            ]);
        }
        $locs = $em->getRepository(Location::class)->findBy(["appartement" => $appart->getId()]);
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
        $user = $this->getUser();
        if (!$user->isVerified()) {
            $this->addFlash('warning', "plsverif");
            return $this->redirectToRoute('appartements');
        }
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
            return $this->redirectToRoute('appartements');
        }

        
        $dates = explode("-", $firstForm->get('date')->getData());
        $dates = array_map(function ($date) {
            return new \DateTime($date);
        }, $dates);
        if ($dates[0] == $dates[1]) {
            $this->addFlash("danger", "samedate");
            return $this->redirectToRoute('appart_detail', ['id' => $id]);
        }
        $days = $dates[0]->diff($dates[1])->days;
        if ($firstForm->isSubmitted() && !$firstForm->isValid()) {
            return $this->redirectToRoute('appart_detail', ['id' => $id]);
        }
        $locs = $em->getRepository(Location::class)->findBy(["appartement" => $id]);
        foreach ($locs as $loc) {
            $startDate = $loc->getDateDebut();
            $endDate = $loc->getDateFin();
            if ($dates[0] >= $startDate && $dates[0] <= $endDate) {
                $this->addFlash("danger", "dateunavailable");
                return $this->redirectToRoute('appart_detail', ['id' => $id]);
            }
            if ($dates[1] >= $startDate && $dates[1] <= $endDate) {
                $this->addFlash("danger", "dateunavailable");
                return $this->redirectToRoute('appart_detail', ['id' => $id]);
            }
            if ($dates[0] <= $startDate && $dates[1] >= $endDate) {
                $this->addFlash("danger", "dateunavailable");
                return $this->redirectToRoute('appart_detail', ['id' => $id]);
            }
        }
        $appart = $em->getRepository(Appartement::class)->find($id);

        return $this->render('appartements/confirm_appart.html.twig', [
            'firstForm' => $firstForm,
            'appart' => $appart,
            'secondForm' => $secondForm,
            'dates' => $dates,
            "days" => $days
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
    
    #[Route("/appartement/create", name: "create_appart")]
    #[IsGranted("ROLE_BAILLEUR")]
    public function createAppart(Request $request, EntityManagerInterface $em, AppartementService $as)
    {
        $res = new Appartement();
        $reservation = $this->createForm(AppartementType::class, $res);
        $builder = $reservation->getConfig()->getFormFactory()->createNamedBuilder("modify_profile", AppartementType::class, $res, array(
            'auto_initialize'=>false // it's important!!!
        ));
        $builder->addEventSubscriber(new ModerationSubscriber($em, $request));
        $reservation = $builder->getForm();
        $reservation->handleRequest($request);
        if ($reservation->isSubmitted() && $reservation->isValid()) {
            $res->setCreatedAt(new \DateTime());
            $res->setUpdatedAt(new \DateTime());
            if ($request->files->get('appartement')['images']) {
                $res->removeImage("house-placeholder.jpg");
                foreach ($request->files->get('appartement')['images'] as $image) {
                    $destination = $this->getParameter('kernel.project_dir').'/var/uploads/appartements';
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename.'-'.uniqid().'.'.$image->guessExtension();
                    $image->move($destination, $newFilename);
                    if ($as->isOk($image)) {
                        $finalDestination = $this->getParameter('kernel.project_dir').'/public/images/appartements';
                        rename($destination."/".$newFilename, $finalDestination."/".$newFilename);
                        $res->addImage($newFilename);
                    }
                }
                $em->persist($res);
                $em->flush();
            }
            return $this->redirectToRoute('profile');
        }
        return $this->render('appartements/create_appart_user.html.twig', ['reservation' => $reservation]);
    }
    #[Route("/appartement/modify/{id}", name: "appart_modify", requirements: ["id" => "\d+"])]
    #[IsGranted("ROLE_BAILLEUR")]
    public function modifyAppart($id, Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $res = $em->getRepository(Appartement::class)->find($id);
        if (!$res || $res->getBailleur()->getResponsable() !== $user) {
            $this->addFlash("danger", "noaccess");
            return $this->redirectToRoute('profile');
        }
        $reservation = $this->createForm(AppartementType::class, $res);
        $builder = $reservation->getConfig()->getFormFactory()->createNamedBuilder("modify_profile", AppartementType::class, $res, array(
            'auto_initialize'=>false // it's important!!!
        ));
        $builder->addEventSubscriber(new ModerationSubscriber($em, $request));
        $reservation = $builder->getForm();
        $reservation->handleRequest($request);
        if ($reservation->isSubmitted() && $reservation->isValid()) {
            $res->setUpdatedAt(new \DateTime());
            $em->persist($res);
            $em->flush();
            return $this->redirectToRoute('profile');
        }
        return $this->render('appartements/update_appart_user.html.twig', ['reservation' => $reservation]);
    }
    #[Route("/appartement/delete/{id}", name: "appart_delete", requirements: ["id" => "\d+"])]
    #[IsGranted("ROLE_BAILLEUR")]
    public function deleteAppart($id, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $res = $em->getRepository(Appartement::class)->find($id);
        if (!$res ||  $res->getBailleur()->getResponsable() !== $user) {
            $this->addFlash("danger", "noaccess");
        } else {
            $em->remove($res);
            $em->flush();
            $this->addFlash("success", "appartdeleted");
        }
            return $this->redirectToRoute('profile');
    }
}
