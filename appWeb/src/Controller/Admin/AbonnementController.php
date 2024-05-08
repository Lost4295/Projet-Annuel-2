<?php
// src/Controller/Admin/AbonnementController.php
namespace App\Controller\Admin;

use App\Entity\Abonnement;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AbonnementController extends AbstractController
{
    #[Route("/adm_tarifications", name: "tarifs")]
    public function Tarifs(EntityManagerInterface $em, AdminUrlGenerator $urlgen): Response
    {
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
            foreach ($abo->getOptions() as $option) {
                $options[$option->getOption()->getNom()][] = ($option->isPresence()) ? "1" : "0";
            }
            $transform["url"][] = $urlgen->setRoute("tarifs_modify", ["id" => $abo->getId()])->generateUrl();
            // $transform["description"][] =  $abo["description"];
            $transform["duree"][] = boolval(rand(0, 1));
        }

        dump($transform, $abos, $options);
        return $this->render('admin/tarifs.html.twig', [
            'abonnements' => $transform,
            'options' => $options
        ]);
    }
    #[Route("/adm_tarification{id}", name: "tarifs_modify")]
    public function TarifModify(EntityManagerInterface $em, Request $request, $id): Response
    {
        $abo = $em->getRepository(Abonnement::class)->find($id);
        $options = [];
        foreach ($abo->getOptions() as $option) {
            $options[$option->getOption()->getNom()] = $option->isPresence();
        }
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
            'options' => $options
        ]);
    }
    // ...
}
