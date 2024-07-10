<?php

namespace App\DataFixtures\Dev;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Abonnement;
use App\Entity\Option;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class AbonnementFixtures extends Fixture implements FixtureGroupInterface
{
    public const GRATUIT= "gratuit";
    public function load(ObjectManager $manager):void
    {
        $opt1= new Option();
        $opt1->setNom("Pas de pubs");

        $opt2= new Option();
        $opt2->setNom("VIP sur les prestas");

        $opt3 = new Option();
        $opt3->setNom("Option spéciale 3");

        $abonnement = new Abonnement();
        $abonnement->setNom("Gratuit")
        ->setTarif("0");
        $this->addReference(self::GRATUIT, $abonnement);

        $abonnement2 = new Abonnement();
        $abonnement2->setNom("baladeur")
        ->setTarif("9.90")
        ->addOption($opt1)
        ->addOption($opt3);

        $abonnement3 = new Abonnement();
        $abonnement3->setNom("Explorateur")
        ->setTarif("19")
        ->addOption($opt1);

        $abonnement4 = new Abonnement();
        $abonnement4->setNom("Globe-trotter")
        ->setTarif("30")
        ->addOption($opt1)
        ->addOption($opt2)
        ->addOption($opt3);

        
        $manager->persist($opt1);
        $manager->persist($opt2);
        $manager->persist($opt3);
        $manager->persist($abonnement);
        $manager->persist($abonnement2);
        $manager->persist($abonnement3);
        $manager->persist($abonnement4);
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev', 'abodev'];
    }
}