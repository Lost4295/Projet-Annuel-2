<?php

//src/Controller/AjaxController.php

namespace App\Controller;

use App\Entity\Appartement;
use App\Entity\Location;
use App\Entity\Note;
use App\Entity\Professionnel;
use App\Service\AppartementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/ajax', name: 'ajax_')]
class AjaxController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route("/search/appart", name: "search")]
    public function search(Request $request)
    {
        $dest = $request->request->get('dest');
        $startdate = $request->request->get('startdate');
        $enddate = $request->request->get('enddate');
        $adults = $request->request->get('adults');
        $children = $request->request->get('children');
        $babies = $request->request->get('babies');

        $apparts = $this->em->getRepository(Appartement::class)->findAppart($dest, $startdate, $enddate, $adults, $children, $babies);

        return $this->json($apparts);
    }

    #[Route("/get/appart", name: "get_apparts")]
    public function getAppart(Request $request)
    {
        $pageSize = $request->get('pageSize');
        $pageNumber = $request->get('pageNumber');
        $appart = $this->em->getRepository(Appartement::class)->getApparts($pageSize, $pageNumber);
        $full = $this->em->getRepository(Appartement::class)->findAll();
        $appart = ["data"=> $appart, "total" => count($full)];
        return $this->json($appart);
    }

    #[Route("/ratingl", name: "rating_l")]
    public function rateLocation(Request $request)
    {
        $rating = $request->request->get('rating');
        $locId = $request->request->get('id');
        $loc = $this->em->getRepository(Location::class)->find($locId);
        $note = new Note();
        $note->setNote($rating);
        $note->setLocation($loc);
        $note->setUser($this->getUser());
        $loc->addNote($note);
        $this->em->persist($note);
        $this->em->persist($loc);
        $this->em->flush();
        return $this->json(['success' => 'true']);
    }

    #[Route("/getrating", name: "get_rating")]
    public function getRating(Request $request, AppartementService $as)
    {
        $locId = $request->get('id');
        $output = $as->updateAppart($locId);
        return $this->json($output);
    }
    
    #[Route("/ratingp", name: "rating_p")]
    public function ratePresta(Request $request)
    {
        $rating = $request->request->get('rating');
        $prestaId = $request->request->get('id');
        $presta = $this->em->getRepository(Professionnel::class)->find($prestaId);
        $note = new Note();
        $note->setNote($rating);
        $note->setPrestataire($presta);
        $note->setUser($this->getUser());
        $presta->addNote($note);
        $this->em->persist($note);
        $this->em->persist($presta);
        $this->em->flush();
        return $this->json(['success' => 'true']);
    }


}
