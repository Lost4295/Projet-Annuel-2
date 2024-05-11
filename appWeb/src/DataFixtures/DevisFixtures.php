<?php

namespace App\DataFixtures;

use App\Entity\Devis;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DevisFixtures extends Fixture
{
    public function load(ObjectManager $manager):void
    {
        for ($i = 1 ; $i < 21 ; $i++){
            $devis = new Devis();
            $devis->setEmail("email$i@pcs.fr");
            $devis->setNom('Nom'.$i);
            $devis->setPrenom('Prenom'.$i);
            $devis->setNumero('06'.rand(10000000,99999999));
            $devis->setTypePresta(rand(1,4));
            $manager->persist($devis);
        }
    }
}