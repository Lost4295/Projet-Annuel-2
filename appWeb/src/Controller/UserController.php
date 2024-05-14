<?php

//src/Controller/UserController.php

namespace App\Controller;

use App\Entity\Appartement;
use App\Entity\Location;
use App\Entity\Professionnel;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\AppartementType;
use App\Form\ModifyProfileType;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{

    #[Route("/profile", name: "profile")]
    #[IsGranted("ROLE_USER")]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $appartements = $locations = $pastlocas = [];
        if ($user->hasRole(User::ROLE_BAILLEUR)) {
            $pro = $em->getRepository(Professionnel::class)->findOneBy(["responsable" => $user->getId()]);
            $appartements = $pro->getAppartements();
        }
        
        if ($user->hasRole(User::ROLE_VOYAGEUR)) {
            $locations = $em->getRepository(Location::class)->findBy(["locataire" => $user->getId()]);
            foreach ($locations as $key => $location) {
                if ($location->getDateDebut() < new \DateTime('now') && $location->getDateFin() < new \DateTime('now')) {
                    $pastlocas[] = $location;
                    unset($locations[$key]);
                }
            }
        }
        $tickets = $em->getRepository(Ticket::class)->findBy(["demandeur" => $user->getId()]);
        // $form = $this->createForm(UserType::class, $user)->handleRequest($request);
        return $this->render('user/profile.html.twig', [
            'message' => 'Hello World!',
            'user' => $user,
            'appartements' => $appartements,
            'locations' => $locations,
            'pastlocations' => $pastlocas,
            'tickets' => $tickets
        ]);
    }

    #[Route("/appartement/create", name: "create_appart")]
    #[IsGranted("ROLE_BAILLEUR")]
    public function createAppart(Request $request, EntityManagerInterface $em)
    {
        $res = new Appartement();
        $reservation = $this->createForm(AppartementType::class, $res);
        $reservation->handleRequest($request);
        if ($reservation->isSubmitted() && $reservation->isValid()) {
            $em->persist($res);
            $em->flush();
            return $this->redirectToRoute('profile');
        }
        return $this->render('appartements/create_appart_user.html.twig', ['reservation' => $reservation]);
    }

    #[Route("profile/modify/", name: "check_infos")]
    #[IsGranted("ROLE_USER")]

    public function checkInfos(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $form = $this->createForm(ModifyProfileType::class,$user)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsVerified(false);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Your profile has been updated successfully. However, you need to verify your email address.');
            return $this->redirectToRoute('profile');
        }

        return $this->render('user/modprofile.html.twig', ['user' => $form]);
    }

    #[Route("/ticket", name: "create_ticket")]
    #[IsGranted("ROLE_USER")]
    public function createTicket(Request $request, EntityManagerInterface $em, TranslatorInterface $trans)
    {
        $info = new Ticket();
        $info
        ->setStatus(TICKET::STATUS_NOUVEAU)
        ->setDateOuverture(new \DateTime('now'))
        ->setDemandeur($this->getUser());
        $reason = $request->get('r');
        if ($reason) {
            switch ($reason) {
                case 'info':
                    $mess =$trans->trans("hereinfo", [], 'messages');
                    $info->setTitre("Changements d'informations")
                    ->setDescription("Je voudrais changer mes informations personnelles. ". $mess)
                    ->setUrgence(TICKET::URGENCE_BASSE)
                    ->setPriority(TICKET::PRIORITY_BASSE)
                    ->setCategory(TICKET::CATEGORY_DEMANDE);
                    break;
                case 'payment':
                    $info = 'Payment';
                    break;
                case 'other':
                    $info = 'Other';
                    break;
                default:
                    break;
            }
        }
        $form = $this->createForm(TicketType::class, $info)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($info);
            $em->flush();
            $this->addFlash('success', 'Your ticket has been created successfully.');
            return $this->redirectToRoute('profile');
        }
        return $this->render('user/create_ticket.html.twig', ['form' => $form]);
    }
}
