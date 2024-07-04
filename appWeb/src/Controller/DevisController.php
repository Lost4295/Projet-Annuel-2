<?php

//src/Controller/DevisController.php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Fichier;
use App\Entity\Professionnel;
use App\EventSubscriber\ModerationSubscriber;
use App\Form\DevisFinType;
use App\Form\DevisType;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;

class DevisController extends AbstractController
{

    #[Route("/devis/{id}", name: "devis_see", requirements: ['id' => '\d+'])]
    public function table($id, EntityManagerInterface $em)
    {
        $devis = $em->getRepository(Devis::class)->find($id);

        return $this->render('devis/devis_see.html.twig', [
            'devis' => $devis,
        ]);
    }


    #[Route("/devis/create", name: "create_devis")]
    public function créer(Request $request, EntityManagerInterface $em)
    {

        $devis = new Devis();
        $data = [];
        if ($this->getUser()) {
            $devis->setUser($this->getUser());
            $data['prenom'] = $this->getUser()->getPrenom();
            $data['nom'] = $this->getUser()->getNom();
            $data['email'] = $this->getUser()->getEmail();
            $data['numero'] = $this->getUser()->getPhoneNumber();
        }
        $form = $this->createForm(DevisType::class, null);
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder("modify_profile", DevisType::class, $data, array(
            'auto_initialize' => false // it's important!!!
        ));
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            if ($data && $data['prenom'] != null) {
                $rprenom = $form->get('prenom')->getConfig()->getOptions();
                $rnom = $form->get('nom')->getConfig()->getOptions();
                $remail = $form->get('email')->getConfig()->getOptions();
                $rnumero = $form->get('numero')->getConfig()->getOptions();
                $attr = ['readonly' => true, 'title' => "nochange", 'class' => 'form-control my-1'];
                $nc = "nochange";
                $wh = ['class' => 'text-white'];
                $rprenom['attr'] = $attr;
                $rprenom['help'] = $nc;
                $rprenom["help_attr"] = $wh;
                $rnom['attr'] = $attr;
                $rnom['help'] = $nc;
                $rnom["help_attr"] = $wh;
                $remail['attr'] = $attr;
                $remail['help'] = $nc;
                $remail["help_attr"] = $wh;
                $rnumero['attr'] = $attr;
                $rnumero['help'] = $nc;
                $rnumero["help_attr"] = $wh;
                $form->add('prenom', TextType::class, $rprenom);
                $form->add('nom', TextType::class, $rnom);
                $form->add('email', TextType::class, $remail);
                $form->add('numero', TextType::class, $rnumero);
            }
        });
        $builder->addEventSubscriber(new ModerationSubscriber($em, $request));
        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $devis->setNom($data['nom']);
            $devis->setPrenom($data['prenom']);
            $devis->setNumero($data['numero']);
            $devis->setEmail($data['email']);
            if ($this->getUser()) {
                $devis->setUser($this->getUser());
            }
            $devis->setTypePresta($data['prestation']);
            $devis->setContactWithPhone(($data['contact']) ? true : false);
            $devis->setDescription($data['description']);
            $em->persist($devis);
            $em->flush();
            $this->addFlash('success', 'devsucess');
            return $this->redirectToRoute('index');
        }
        return $this->render('devis/devis.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route("/devis_pick/{id}", name: "devis_pick")]
    public function pick($id, EntityManagerInterface $em)
    {
        $devis = $em->getRepository(Devis::class)->find($id);
        if (!$devis) {
            $this->addFlash('danger', 'deverror');
            return $this->json(["success" => false]);
        }
        $presta = $em->getRepository(Professionnel::class)->findOneBy(["responsable" => $this->getUser()->getId()]);
        if (!$presta) {
            $this->addFlash('danger', 'deverror');
            return $this->json(["success" => false]);
        }
        $devis->setPrestataire($presta);
        $em->persist($devis);
        $em->flush();
        return $this->json(["success" => true]);
    }
    #[Route("/devis/make/{id}", name: "devis_finalize")]
    public function finalize($id, EntityManagerInterface $em, Request $request, MailerController $mel,)
    {
        $devis = $em->getRepository(Devis::class)->find($id);
        if (!$devis) {
            $this->addFlash('danger', 'deverror');
            return $this->redirectToRoute("profile");
        }
        $presta = $em->getRepository(Professionnel::class)->findOneBy(["responsable" => $this->getUser()->getId()]);
        if (!$presta || $presta != $devis->getPrestataire()) {
            $this->addFlash('danger', 'deverror');
            return $this->redirectToRoute("profile");
        }
        $devisFinType = $this->createForm(DevisFinType::class, $devis);
        $devisFinType->handleRequest($request);
        if ($devisFinType->isSubmitted() && $devisFinType->isValid()) {
            $esttime = $devisFinType->get('estimatedTime')->getData();
            $devis->setStartDate($devisFinType->get('startDate')->getData());
            $devis->setEndDate($devisFinType->get('endDate')->getData());
            $devis->setEstimatedTime($esttime);
            $devis->setPrix($devisFinType->get('prix')->getData());
            $devis->setToValidate(true);
            $devis->setTurn(true);
            if (!$devis->getUser()) {
                $url = $request->getSchemeAndHttpHost();
                $sid = md5($devis->getId() . $devis->getNom() . $devis->getPrenom());
                $devis->setSid($sid);
                $mel->sendMail($devis->getEmail(), "devis", "Bonjour, votre devis a été finalisé, veuillez le valider en cliquant sur le lien suivant : <a href='$url/devis/validate/" . $devis->getId() . "?sid=$sid'>Valider</a>");
            }
            $em->persist($devis);
            $em->flush();
            $this->addFlash('success', 'devsucess');
            return $this->redirectToRoute("profile");
        }
        return $this->render('devis/devis_fin.html.twig', [
            'form' => $devisFinType,
        ]);
    }

    #[Route("/devis/validate/{id}", name: "devis_validate")]
    public function validate($id, EntityManagerInterface $em, Request $request, PdfService $pdf)
    {
        $devis = $em->getRepository(Devis::class)->find($id);
        if (!$devis) {
            $this->addFlash('danger', 'deverror');
            return $this->redirectToRoute("profile");
        }
        if ($devis->getToValidate() && ($devis->getSid() == $request->get('sid') || $devis->getUser() == $this->getUser())) {
            $data = $pdf->createDevisPdf($devis);
            if (file_exists($data[0])) {
                $file = new Fichier();
                $file->setNom($data[1]);
                $file->setPath($data[1]);
                $file->setUser($devis->getUser());
                $file->setType("devis");
                $file->setSize($pdf::human_filesize(filesize($data[0])));
                $file->setDate(new \DateTime());
                $em->persist($file);
            } else {
                $this->addFlash('danger', 'errgeneratingpdf');
                return $this->redirectToRoute("profile");
            }
            $devis->setOk(true);
            $em->persist($devis);
            $em->flush();
            $this->addFlash('success', 'devsucess');
            return $this->redirectToRoute("profile");
        }
        $this->addFlash('danger', 'deverrorunexpected');
        return $this->redirectToRoute("profile");
    }


    #[Route("/devis/modify/{id}", name: "devis_modify")]
    public function modify($id, EntityManagerInterface $em, Request $request)
    {
        $devis = $em->getRepository(Devis::class)->find($id);
        if (!$devis) {
            $this->addFlash('danger', 'deverror');
            return $this->redirectToRoute("profile");
        }
        if ($devis->getUser()->isEqualTo($this->getUser()) && $devis->getPrestataire()->getResponsable()->isEqualTo($this->getUser())) {
            $this->addFlash('danger', 'deverrorunexpected');
            return $this->redirectToRoute("profile");
        }
        $devisFinType = $this->createForm(DevisFinType::class, $devis);
        $devisFinType->handleRequest($request);
        if ($devisFinType->isSubmitted() && $devisFinType->isValid()) {
            $devis->setStartDate($devisFinType->get('startDate')->getData());
            $devis->setEndDate($devisFinType->get('endDate')->getData());
            $esttime = $devisFinType->get('estimatedTime')->getData();
            $devis->setEstimatedTime($esttime);
            $devis->setPrix($devisFinType->get('prix')->getData());
            $devis->setToValidate(true);
            $devis->setOk(false);
            $devis->setTurn(false);
            $em->persist($devis);
            $em->flush();
            $this->addFlash('success', 'devsucess');
            return $this->redirectToRoute("profile");
        }
        return $this->render('devis/devis_fin.html.twig', [
            'form' => $devisFinType,
        ]);
    }

    #[Route("/devis/refuse/{id}", name: "devis_refuse")]
    public function refuse($id, EntityManagerInterface $em)
    {
        $devis = $em->getRepository(Devis::class)->find($id);
        if (!$devis) {
            $this->addFlash('danger', 'deverror');
            return $this->redirectToRoute("profile");
        }
        if ($devis->getUser()->isEqualTo($this->getUser()) && $devis->getPrestataire()->getResponsable()->isEqualTo($this->getUser())) {
            dd($devis->getUser()->isEqualTo($this->getUser()), $devis->getPrestataire()->getResponsable()->isEqualTo($this->getUser()));
            $this->addFlash('danger', 'deverrorunexpected');
            return $this->redirectToRoute("profile");
        }
        $em->remove($devis);
        $em->flush();
        $this->addFlash('success', 'devsucess');
        return $this->redirectToRoute("profile");
    }

    static function dateIntervalToHumanString(\DateInterval $interval)
    {
        $units = array("y" => "year", "m" => "month", "d" => "day", "h" => "hour", "i" => "minute", "s" => "second");
        $specString = "";
        foreach ($units as $prop => $spec) {
            if ($number = $interval->$prop) {
                $specString .= $number . " " . $spec;
                $specString .= $number > 1 ? "s " : " ";
            }
        }
        return trim($specString);
    }
}
