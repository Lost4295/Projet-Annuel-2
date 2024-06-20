<?php

//src/Controller/DevisController.php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Fichier;
use App\EventSubscriber\ModerationSubscriber;
use App\Form\DevisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;

class DevisController extends AbstractController
{

    #[Route("/devis", name: "devis")]
    public function table(EntityManagerInterface $em)
    {
        $devis = $em->getRepository(Fichier::class)->findBy(["type" => "devis"]);

        return $this->render('devis.html.twig', [
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
            $devis->setTypePresta($data['prestation']);
            $devis->setContactWithPhone(($data['contact'])?true:false);
            $devis->setDescription($data['description']);
            $em->persist($devis);
            $em->flush();
            $this->addFlash('success', 'devsucess');
            return $this->redirectToRoute('index');
        }
        return $this->render('devis.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route("/devis_pick/{id}", name: "devis_pick")]
    public function pick($id, EntityManagerInterface $em)
    {
        $devis = $em->getRepository(Devis::class)->find($id);
        if ($devis) {
            $devis->setPrestataire($this->getUser());
            $em->persist($devis);
            $em->flush();
            return $this->json(["success" => true]);
        } else {
            $this->addFlash('danger', 'deverror');
            return $this->json(["success" => false]);
        }
    }
}
