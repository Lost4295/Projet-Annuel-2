<?php
// src/Controller/Admin/AbonnementController.php
namespace App\Controller\Admin;

use App\Entity\Abonnement;
use App\Entity\Option;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AbonnementController extends AbstractController
{
    #[Route("/adm_tarifications", name: "tarifs")]
    public function Tarifs(EntityManagerInterface $em, AdminUrlGenerator $urlgen): Response
    {
        $options = $em->getRepository(Option::class)->findAll();
        $abos = $em->getRepository(Abonnement::class)->findAll();
        $tab = [];
        $transform = [];
        $transform["key"] = [null];
        $transform["nom"] = [null];
        $transform["prix"] = [null];
        $transform["duree"] = [null];
        $transform["url"] = [null];
        foreach ($abos as $key => $abo) {
            $transform["key"][] = $key;
            $transform["prix"][] =  $abo->getTarif();
            $transform["nom"][] =  $abo->getNom();

            foreach ($options as $option) {
                $urlopt[$option->getNom()]["upd"] = $urlgen->setRoute("option_modify", ["id" => $option->getId()])->generateUrl();
                $urlopt[$option->getNom()]["del"] = $urlgen->setRoute("option_delete", ["id" => $option->getId()])->generateUrl();
                if ($abo->getOptions()->contains($option)) {
                    $tab[$option->getNom()][$key] = 1;
                } else {
                    $tab[$option->getNom()][$key] = 0;
                }
            }

            $transform["url"][$abo->getId()]["upd"] = $urlgen->setRoute("tarifs_modify", ["id" => $abo->getId()])->generateUrl();
            $transform["url"][$abo->getId()]["del"] = $urlgen->setRoute("del_abonnement", ["id" => $abo->getId()])->generateUrl();
            $transform["duree"][] = boolval(rand(0, 1));
        }

        // dump($transform, $abos, $tab, $form);
        return $this->render('admin/tarifs.html.twig', [
            'abonnements' => $transform,
            'options' => $tab,
            'urlopt' => $urlopt,
            "add_abos" => $urlgen->setRoute("add_abos")->generateUrl(),
            "add_opt" => $urlgen->setRoute("add_opt")->generateUrl(),
        ]);
    }
    #[Route("/adm_tarification/{id}", name: "tarifs_modify")]
    public function TarifModify(EntityManagerInterface $em, Request $request, $id, AdminUrlGenerator $urlgen): Response
    {
        $abo = $em->getRepository(Abonnement::class)->find($id);
        $options = $em->getRepository(Option::class)->findAll();
        $list = [];
        foreach ($abo->getOptions() as $option) {
            $list[$option->getNom()] = $option;
        }
        $id = $abo->getId();
        $form = $this->createFormBuilder(null)
            ->add('tarif', null, ["attr" => ["class" => 'form-control my-2'], "data" => $abo->getTarif()])
            ->add('nom', null, ["attr" => ["class" => 'form-control my-2'], "data" => $abo->getNom()]);
        foreach ($options as $option) {
            $val = @boolval($list[$option->getNom()]);
            $form->add('option' . $option->getId(), CheckboxType::class, ["attr" => ["class" => ' my-2'], "label" => $option->getNom(), "data" => $val, "required" => false]);
        }
        $form = $form->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $abo->setTarif($data['tarif']);
            $abo->setNom($data['nom']);
            foreach ($abo->getOptions() as $option) {
                $abo->removeOption($option);
            }
            foreach ($options as $option) {
                if ($data['option' . $option->getId()]) {
                    $abo->addOption($option);
                }
            }
            $em->persist($abo);
            $em->flush();
            return $this->redirect($urlgen->setRoute("tarifs")->generateUrl());
        }

        return $this->render('admin/tarif.html.twig', [
            'abo' => $abo,
            'options' => $list,
            'form' => $form,
            'id' => $id,
        ]);
    }

    #[Route("/adm_del_tarif", name: "del_abonnement")]
    public function delAbonnement(EntityManagerInterface $em, Request $request, AdminUrlGenerator $urlgen): Response
    {
        $id = $request->get('id');
        $abo = $em->getRepository(Abonnement::class)->find($id);
        $em->remove($abo);
        $em->flush();
        return $this->redirect($urlgen->setRoute("tarifs")->generateUrl());
    }

    #[Route("/add/abo", name: "add_abos")]
    public function createAbonnement(EntityManagerInterface $em, Request $request, AdminUrlGenerator $urlgen)
    {
        $options = $em->getRepository(Option::class)->findAll();
        $form = $this->createFormBuilder(null)
            ->add('tarif', null, ["attr" => ["class" => 'form-control my-2']])
            ->add('nom', null, ["attr" => ["class" => 'form-control my-2']]);
        foreach ($options as $option) {
            $form->add('option' . $option->getId(), CheckboxType::class, ["attr" => ["class" => ' my-2'], "label" => $option->getNom(), "required" => false]);
        }
        $form = $form->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $abo = new Abonnement();
            $data = $form->getData();
            $abo->setTarif($data['tarif']);
            $abo->setNom($data['nom']);
            foreach ($abo->getOptions() as $option) {
                $abo->removeOption($option);
            }
            foreach ($options as $option) {
                if ($data['option' . $option->getId()]) {
                    $abo->addOption($option);
                }
            }
            $em->persist($abo);
            $em->flush();
            return $this->redirect($urlgen->setRoute("tarifs")->generateUrl());
        }
        return $this->render('admin/tarif.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route("/option/{id}/modify", name: "option_modify")]
    public function updateOption(Request $request, EntityManagerInterface $em, AdminUrlGenerator $urlgen)
    {
        $form = $this->createFormBuilder(null)
            ->add('nom', TextType::class, ["attr" => ["class" => 'form-control my-2']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $abo = $em->getRepository(Option::class)->find($request->get('id'));
            $abo->setNom($data['nom']);
            $em->persist($abo);
            $em->flush();
            return $this->redirect($urlgen->setRoute("tarifs")->generateUrl());
        }
        return $this->render('admin/option.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route("/option/{id}/delete", name: "option_delete")]
    public function deleteOption(EntityManagerInterface $em, Request $request, AdminUrlGenerator $urlgen)
    {
        $id = $request->get('id');
        $abo = $em->getRepository(Option::class)->find($id);
        $em->remove($abo);
        $em->flush();
        return $this->redirect($urlgen->setRoute("tarifs")->generateUrl());
    }

    #[Route("/addopt",name: "add_opt")]
    public function createOption(Request $request, EntityManagerInterface $em, AdminUrlGenerator $urlgen)
    {
        $form = $this->createFormBuilder(null)
            ->add('nom', TextType::class, ["attr" => ["class" => 'form-control my-2']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $abo = new Option();
            $abo->setNom($data['nom']);
            $em->persist($abo);
            $em->flush();
            return $this->redirect($urlgen->setRoute("tarifs")->generateUrl());
        }
        return $this->render('admin/option.html.twig', [
            'form' => $form,
        ]);
    }
    // ...
}
