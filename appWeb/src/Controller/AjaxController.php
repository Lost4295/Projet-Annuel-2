<?php

//src/Controller/AjaxController.php

namespace App\Controller;

use App\Entity\Appartement;
use App\Entity\Location;
use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/ajax', name: 'ajax_')]
class AjaxController extends AbstractController
{

    #[Route("/search/appart", name: "search")]
    public function search(Request $request, EntityManagerInterface $em)
    {
        $dest = $request->request->get('dest');
        $startdate = $request->request->get('startdate');
        $enddate = $request->request->get('enddate');
        $adults = $request->request->get('adults');
        $children = $request->request->get('children');
        $babies = $request->request->get('babies');

        $apparts = $em->getRepository(Appartement::class)->findAppart($dest, $startdate, $enddate, $adults, $children, $babies);

        dump($apparts);
        return $this->json($apparts);
    }

    #[Route("/rating", name: "rating")]
    public function rateLocation(Request $request, EntityManagerInterface $em)
    {
        $rating = $request->request->get('rating');
        $locId = $request->request->get('id');
        $loc = $em->getRepository(Location::class)->find($locId);
        $note = new Note();
        $note->setNote($rating);
        $note->setLocation($loc);
        $note->setUser($this->getUser());
        $loc->addNote($note);
        $em->persist($note);
        $em->persist($loc);
        $em->flush();
        return $this->json(['success' => 'true']);
    }

    #[Route("/getrating", name: "get_rating")]
    public function getRating(Request $request, EntityManagerInterface $em)
    {
        $locId = $request->get('id');
        $app = $em->getRepository(Appartement::class)->find($locId);
        $locs = $app->getLocations();
        $notes = [];
        foreach ($locs as $loc) {
            foreach ($loc->getNotes() as $note) {
                $notes[] = $note->getNote();
            }
        }
        $total_review = $total_user_rating = $five_star_review = $four_star_review
            = $three_star_review = $two_star_review = $one_star_review = 0;

        foreach ($notes as $note) {
            switch ($note) {
                case '5':
                    $five_star_review++;
                    break;
                case '4':
                    $four_star_review++;
                    break;
                case '3':
                    $three_star_review++;
                    break;
                case '2':
                    $two_star_review++;
                    break;
                case '1':
                    $one_star_review++;
                    break;
                default:
                    break;
            }
            $total_review++;
            $total_user_rating += $note;
        }
        if ($total_review == 0) {
            $average_rating = 0;
        } else {
            $average_rating = $total_user_rating / $total_review;
        }
        $output = [
            'average_rating'    =>    number_format($average_rating, 1),
            'total_review'        =>    $total_review,
            'five_star_review'    =>    $five_star_review,
            'four_star_review'    =>    $four_star_review,
            'three_star_review'    =>    $three_star_review,
            'two_star_review'    =>    $two_star_review,
            'one_star_review'    =>    $one_star_review,
        ];
        $app->setNote($average_rating);
        $em->persist($app);
        $em->flush();
        return $this->json($output);
    }
}
