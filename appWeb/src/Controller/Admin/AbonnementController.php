<?php
// src/Controller/Admin/AbonnementController.php
namespace App\Controller\Admin;

use App\Entity\Abonnement;
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
        $form = $this->createFormBuilder();
        $abos = $em->getRepository(Abonnement::class)->findAll();
        $transform = [];
        $options = [];
        $transform["key"] = [null];
        $transform["nom"] = [null];
        $transform["prix"] = [null];
        $transform["duree"] = [null];
        $transform["url"] = [null];
        foreach ($abos as $key => $abo) {
            $transform["key"][] = $key;
            $transform["prix"][] =  $abo->getTarif();
            $transform["nom"][] =  $abo->getNom();

            $form->add($key+1, TextType::class, ["attr" => ["class" => 'form-control my-2']]);
            $form->add("tarif$key", NumberType::class, ["attr" => ["class" => 'form-control my-2']]);
            foreach ($abo->getOptions() as $option) {
                $options[$option->getOption()->getNom()][] = ($option->isPresence()) ? "1" : "0";
                $form->add($option->getOption()->getId() . $key, CheckboxType::class, ["attr" => ["class" => 'form-control my-2'], "label" => $option->getOption()->getNom(), "data" => boolval($options[$option->getOption()->getNom()][$key])]);
            }
            $transform["url"][$abo->getId()]["upd"] = $urlgen->setRoute("tarifs_modify", ["id" => $abo->getId()])->generateUrl();
            $transform["url"][$abo->getId()]["del"] = $urlgen->setRoute("del_abonnement", ["id" => $abo->getId()])->generateUrl();
            // $transform["description"][] =  $abo["description"];
            $transform["duree"][] = boolval(rand(0, 1));
        }
        $form = $form->getForm();

        dump($transform, $abos, $options, $form);
        return $this->render('admin/tarifs.html.twig', [
            'abonnements' => $transform,
            'options' => $options,
            'form' => $form
        ]);
    }
    #[Route("/adm_tarification/{id}", name: "tarifs_modify")]
    public function TarifModify(EntityManagerInterface $em, Request $request, $id): Response
    {
        $abo = $em->getRepository(Abonnement::class)->find($id);
        $options = [];
        foreach ($abo->getOptions() as $option) {
            $options[$option->getOption()->getNom()] = $option->isPresence();
        }
        $id = $abo->getId();
        $form = $this->createFormBuilder($abo)
            ->add('tarif', null, ["attr"=>["class"=>'form-control my-2']])
            ->add('nom', null, ["attr"=>["class"=>'form-control my-2']])
            ->getForm();

        if ($request->isMethod('POST')) {
            $abo->setTarif($request->request->get('tarif'));
            $abo->setNom($request->request->get('nom'));
            // $abo->setDuree($request->request->get('duree'));
            $em->persist($abo);
            $em->flush();
            foreach ($abo->getOptions() as $option) {
                $option->setPresence($request->request->get($option->getOption()->getNom()));
                $em->persist($option);
            }
            $em->flush();
            return $this->redirectToRoute('tarifs');
        }
        return $this->render('admin/tarif.html.twig', [
            'abo' => $abo,
            'options' => $options,
            'form'=> $form,
            'id' => $id
        ]);
    }
    #[Route("/adm_update_tarif", name: "update_abonnement")]
    public function updateAbonnement(EntityManagerInterface $em, Request $request, AdminUrlGenerator $urlgen): Response
    {
        $id = $request->get('id');
        $abo = $em->getRepository(Abonnement::class)->find($id);
        $form = $request->request->all()["form"];
        $abo->setTarif($form["tarif"]);
        $abo->setNom($form["nom"]);
        $em->persist($abo);
        $em->flush();
        return $this->redirect($urlgen->setRoute("tarifs")->generateUrl());
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
    // ...
}
