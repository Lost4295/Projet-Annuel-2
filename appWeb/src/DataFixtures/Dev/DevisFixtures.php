<?php

namespace App\DataFixtures\Dev;

use App\Entity\Devis;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class DevisFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager):void
    {
        for ($i = 1 ; $i < 21 ; $i++){
            $devis = new Devis();
            $devis->setEmail("email$i@pcs.fr");
            $devis->setNom('Nom'.$i);
            $devis->setPrenom('Prenom'.$i);
            $devis->setNumero('06'.rand(10000000,99999999));
            $devis->setTypePresta(rand(1,6));
            $manager->persist($devis);
        }
    }
    public static function getGroups(): array
    {
        return ['dev', 'devisdev'];
    }
}
