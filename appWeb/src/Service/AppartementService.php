<?php

namespace App\Service;

use App\Entity\Appartement;
use Doctrine\ORM\EntityManagerInterface;

class AppartementService {
    
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function updateAppart($locId)
    {
        $app = $this->em->getRepository(Appartement::class)->find($locId);
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
        $this->em->persist($app);
        $this->em->flush();
        return $output;
    }

    public function isOk($file)
    {
        //TODO vérifier avec l'API Si c'est un apparrtement
        return true;
    }
}
