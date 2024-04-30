<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Abonnement;
use App\Entity\Options;

class AbonnementFixtures extends Fixture
{
    public function load(ObjectManager $manager):void
    {
        $opt1= new Options();
        $opt1->setNom("Option 1");
        $opt2= new Options();
        $opt2->setNom("Option 2");

        
        $abonnement = new Abonnement();
        $abonnement->setNom("Gratuit")
        ->setTarif("0")->addOption($opt1);
        
        $abonnement = new Abonnement();
        $abonnement->setNom("Gratuit")
        ->setTarif("0")->addOption($opt1);

        $manager->persist($abonnement);
            $manager->flush();
    }
}