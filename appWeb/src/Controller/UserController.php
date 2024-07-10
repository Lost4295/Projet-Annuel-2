<?php

//src/Controller/UserController.php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Devis;
use App\Entity\Fichier;
use App\Entity\Location;
use App\Entity\Option;
use App\Entity\Professionnel;
use App\Entity\Ticket;
use App\Entity\User;
use App\EventSubscriber\ModerationSubscriber;
use App\Form\DateMoisType;
use App\Form\ModifyProfileType;
use App\Form\PrestatypeType;
use App\Form\TicketType;
use App\Form\VerifyType;
use App\Form\WorkDaysType;
use App\Service\AppartementService;
use App\Service\PdfService;
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
    public function profile(EntityManagerInterface $em, AppartementService $as, Request $request, PdfService $pdf): Response
    {
        $user = $this->getUser();
        $appartements = $locations = $pastlocas = [];
        if ($user->hasRole(User::ROLE_PRESTA) || $user->hasRole(User::ROLE_BAILLEUR)) {
            $pro = $em->getRepository(Professionnel::class)->findOneBy(["responsable" => $user->getId()]);
            $as->updateProfessionnel($pro->getId());
        }
        if ($user->hasRole(User::ROLE_BAILLEUR)) {
            $appartements = $pro->getAppartements();
            $data = [];
            foreach ($appartements as $appartement) {
                $data[$appartement->getTitre()] = $as->updateAppart($appartement->getId());
            }
            $devis = $em->getRepository(Devis::class)->findBy(["user" => $user->getId(), "isOk" => false, "turn" => true]);
            $validDevs = $em->getRepository(Devis::class)->findBy(["user" => $user->getId(), "isOk" => true]);
        }
        if ($user->hasRole(User::ROLE_PRESTA)) {
            $services = $pro->getServices();
            $dataserv = [];
            foreach ($services as $service) {
                $dataserv[$service->getTitre()] = $as->updateService($service->getId());
            }
            if ($pro->getPrestaType() == null) {
                $this->addFlash('danger', 'chooprestype');
            } else {
                $devis = $em->getRepository(Devis::class)->findBy(["isOk" => false, "turn" => false]);
                $unPickedDevis = $em->getRepository(Devis::class)->findBy(["prestataire" => null, "typePresta" => $pro->getPrestaType()]);
                $vDevs = $em->getRepository(Devis::class)->findBy(["prestataire" => $pro->getId(), "isOk" => true]);
                foreach ($vDevs as $vDev) {
                    $dev = [];
                    $dev["id"] = $vDev->getId();
                    $dev["title"] = $vDev->getNom() . " " . $vDev->getPrenom();
                    $dev["start"] = $vDev->getStartDate()->format('Y-m-d H:i:s');
                    $dev["end"] = $vDev->getEndDate()->format('Y-m-d H:i:s');
                    $dev["color"] = self::randomcolor();
                    $dev["overlap"] = false;
                    $dev["constraint"] = 'businessHours';
                    $dev["url"] = $this->generateUrl('devis_see', ['id' => $vDev->getId()]);
                    $validDevs[] = $dev;
                }
            }
            $workdays = $pro->getWorkDays();
            $workform = $this->createForm(WorkDaysType::class, $workdays);
            $workform->handleRequest($request);
            if ($workform->isSubmitted() && $workform->isValid()) {
                $data = $workform->getData();
                $pro->setWorkDays($data['days']);
                $pro->setStartHour($data['sth']->format('H:i'));
                $pro->setEndHour($data['enh']->format('H:i'));
                $em->persist($pro);
                $em->flush();
                $this->addFlash('success', "sucworkd");
            }
            if ($pro->getPrestaType() == null) {

                $prestatype = $pro->getPrestatype();
                $prestatype = $this->createForm(PrestatypeType::class, $prestatype);
                $prestatype->handleRequest($request);
                if ($prestatype->isSubmitted() && $prestatype->isValid()) {
                    $data = $prestatype->getData();
                    $pro->setPrestatype($data['type']);
                    $em->persist($pro);
                    $em->flush();
                    $this->addFlash('success', "sucworkd");
                }
            }
        }
        $invoce = $this->createForm(DateMoisType::class);
        $invoce->handleRequest($request);

        if ($invoce->isSubmitted() && $invoce->isValid() && $invoce->get('valider')->isClicked()) {
            if ($user->hasRole(User::ROLE_BAILLEUR) || $user->hasRole(User::ROLE_PRESTA)) {
                $devis = $em->getRepository(Devis::class)->findBy(["user" => $user, "isOk" => true]);
                $path = $pdf->generateMonthlyDevisPdf($devis, $user);
                // Redirect 
                return $this->file($path[0]);
            } else {
                $invoices = $em->getRepository(Location::class)->findMonthlyInvoicesByUser($user, new \DateTime('first day of ' . $invoce->getData()['mois']), new \DateTime('last day of ' . $invoce->getData()['mois']));
                if (empty($invoices)) {
                    $this->addFlash('warning', 'No invoices found for this month.');
                    return $this->redirectToRoute('profile');
                }
                $path = $pdf->generateMonthlyPdf($invoices, $user);
                // Redirect or display the PDF
                return $this->file($path[0]);
            }
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
        $factu = $em->getRepository(Fichier::class)->findBy(["user" => $user->getId()]);

        $tickets = $em->getRepository(Ticket::class)->findBy(["demandeur" => $user->getId()]);
        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'appartements' => $appartements,
            'locations' => $locations,
            'pastlocations' => $pastlocas,
            'tickets' => $tickets ?? null,
            'data' => $data ?? null,
            'pro' => $pro ?? null,
            'services' => $services ?? null,
            'dataserv' => $dataserv ?? null,
            'devis' => $devis ?? null,
            'workform' => $workform ?? null,
            'unpicked' => $unPickedDevis ?? null,
            'facture' => $factu ?? null,
            'prestype' => $prestatype ?? null,
            'invoce' => $invoce ?? null,
            'validDevs' => $validDevs ?? null
        ]);
    }

    public function randomcolor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    #[Route("profile/modify/", name: "check_infos")]
    #[IsGranted("ROLE_USER")]

    public function checkInfos(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        if ($user->hasRole(User::ROLE_BAILLEUR) || $user->hasRole(User::ROLE_PRESTA)) {
            $pro = $em->getRepository(Professionnel::class)->findOneBy(["responsable" => $user->getId()]);
        }
        $form = $this->createForm(ModifyProfileType::class, $user);
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder("modify_profile", ModifyProfileType::class, $user, array(
            'auto_initialize' => false // it's important!!!
        ));
        $builder->addEventSubscriber(new ModerationSubscriber($em, $request));
        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->hasRole(User::ROLE_BAILLEUR) || $user->hasRole(User::ROLE_PRESTA)) {
                $pro = $em->getRepository(Professionnel::class)->findOneBy(["responsable" => $user->getId()]);
                $image = $request->files->get('modify_profile')['image'];
                $destination = $this->getParameter('kernel.project_dir') . '/var/uploads/presta';
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $image->guessExtension();
                $image->move($destination, $newFilename);
                $finalDestination = $this->getParameter('kernel.project_dir') . '/public/images/presta';
                rename($destination . "/" . $newFilename, $finalDestination . "/" . $newFilename);
                $pro->setImage($newFilename);
                $em->persist($pro);
            }
            $user->setIsVerified(false);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Your profile has been updated successfully. However, you need to verify your email address.');
            return $this->redirectToRoute('profile');
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $s = true;
        }

        return $this->render('user/modprofile.html.twig', ['user' => $form, 'pro' => $pro ?? null, 'submitted' => $s ?? null]);
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
                    $mess = $trans->trans("hereinfo", [], 'messages');
                    $info->setTitre("Changements d'informations")
                        ->setDescription("Je voudrais changer mes informations personnelles. " . $mess)
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
        $form = $this->createForm(TicketType::class, $info);
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder("modify_profile", TicketType::class, $info, array(
            'auto_initialize' => false // it's important!!!
        ));
        $builder->addEventSubscriber(new ModerationSubscriber($em, $request));
        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($info);
            $em->flush();
            $this->addFlash('success', 'tickcrt');
            return $this->redirectToRoute('profile');
        }
        return $this->render('user/create_ticket.html.twig', ['form' => $form]);
    }

    #[Route("/abonnements", name: "abos")]
    public function abonnements(EntityManagerInterface $em)
    {
        $user = $this->getUser();
        if ($user) {
            $abonnement = $user->getAbonnement();
        }
        $abos = $em->getRepository(Abonnement::class)->findAll();
        $options = $em->getRepository(Option::class)->findAll();
        $tab = [];
        $transform = [];
        $transform["key"] = [null];
        $transform["nom"] = [null];
        $transform["prix"] = [null];
        $transform["url"] = [null];
        foreach ($abos as $key => $abo) {
            $transform["key"][] = $key;
            $transform["prix"][] =  $abo->getTarif();
            $transform["nom"][] =  $abo->getNom();
            foreach ($options as $option) {
                if ($abo->getOptions()->contains($option)) {
                    $tab[$option->getNom()][$key] = 1;
                } else {
                    $tab[$option->getNom()][$key] = 0;
                }
            }
            $transform["url"][$abo->getId()]["link"] = $this->generateUrl('change_abo', ['id' => $abo->getId()]);
            $transform["url"][$abo->getId()]["link"] = $this->generateUrl('stripe_abos', ['id' => $abo->getId()]);
        }
        return $this->render('user/abonnements.html.twig', [
            'abonnements' => $transform,
            'options' => $tab,
            'abouser' => $abonnement ?? null
        ]);
    }

    #[Route("/abonnements/getnew/{id}", name: "change_abo")]
    #[IsGranted("ROLE_USER")]
    public function changeAbo(EntityManagerInterface $em, $id)
    {
        $user = $this->getUser();
        $abo = $em->getRepository(Abonnement::class)->find($id);
        $user->setAbonnement($abo);
        $em->persist($user);
        $em->flush();
        $this->addFlash('success', 'subsuced');
        return $this->redirectToRoute('abos');
    }

    #[Route('/file/download/{id}', name: 'download_file')]
    #[IsGranted("ROLE_USER")]
    public function downloadFile($id, EntityManagerInterface $em)
    {
        $file = $em->getRepository(Fichier::class)->find($id);
        $path = $this->getParameter('kernel.project_dir') . '/public/files/pdfs/' . $file->getPath();
        if ($file->getUser() == $this->getUser()) {
            return $this->file($path);
        } else {
            $this->addFlash('danger', 'nodl');
            return $this->redirectToRoute('profile');
        }
    }

    #[Route("/verify", name: "verifyform")]
    public function verifyForm(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'notallowed');
            return $this->redirectToRoute('app_login');
        }
        if (!$user->isVerified()) {
            $this->addFlash('danger', 'alreadyverif');
            return $this->redirectToRoute('profile');
        }
        if (!$user->hasRole(User::ROLE_BAILLEUR) && !$user->hasRole(User::ROLE_PRESTA)) {
            $this->addFlash('danger', 'notallowed');
            return $this->redirectToRoute('profile');
        }
        $pro = $em->getRepository(Professionnel::class)->findOneBy(["responsable" => $user->getId()]);
        $form = $this->createForm(VerifyType::class, $pro);
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder("verify_pro", VerifyType::class, $pro, array(
            'auto_initialize' => false // it's important!!!
        ));
        $builder->addEventSubscriber(new ModerationSubscriber($em, $request));
        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pro->setJustification($form->get('justification')->getData());
            $em->persist($pro);
            $em->flush();
            $this->addFlash('success', 'verif');
            return $this->redirectToRoute('profile');
        }
        return $this->render('user/verify.html.twig', ['form' => $form->createView()]);
    }
}
