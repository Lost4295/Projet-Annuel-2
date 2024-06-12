<?php

namespace App\DataFixtures\Dev;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Abonnement;
use App\Entity\Option;
use App\Entity\OptionsAbonnement;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class AbonnementFixtures extends Fixture implements FixtureGroupInterface
{
    public const GRATUIT= "gratuit";
    public function load(ObjectManager $manager):void
    {
        $opt1= new Option();
        $opt1->setNom("Pubs");

        $opt2= new Option();
        $opt2->setNom("VIP sur les prestas");

        $opt3 = new Option();
        $opt3->setNom("toto");

        $abonnement = new Abonnement();
        $abonnement->setNom("Gratuit")
        ->setTarif("0");
        $this->addReference(self::GRATUIT, $abonnement);

        $abonnement2 = new Abonnement();
        $abonnement2->setNom("baladeur")
        ->setTarif("9.90");

        $abonnement3 = new Abonnement();
        $abonnement3->setNom("Explorateur")
        ->setTarif("19");

        $abonnement4 = new Abonnement();
        $abonnement4->setNom("Globe-trotter")
        ->setTarif("30");

        $ao1 = new OptionsAbonnement();
        $ao1->setPresence(false)
        ->setAbonnement($abonnement)
        ->setOption($opt1);

        $ao2 = new OptionsAbonnement();
        $ao2->setPresence(true)
        ->setAbonnement($abonnement2)
        ->setOption($opt1);

        $ao3 = new OptionsAbonnement();
        $ao3->setPresence(true)
        ->setAbonnement($abonnement3)
        ->setOption($opt1);

        $ao4 = new OptionsAbonnement();
        $ao4->setPresence(true)
        ->setAbonnement($abonnement4)
        ->setOption($opt1);

        $ao11= new OptionsAbonnement();
        $ao11->setPresence(false)
        ->setAbonnement($abonnement)
        ->setOption($opt2);

        $ao12 = new OptionsAbonnement();
        $ao12->setPresence(true)
        ->setAbonnement($abonnement2)
        ->setOption($opt2);

        $ao13 = new OptionsAbonnement();
        $ao13->setPresence(false)
        ->setAbonnement($abonnement3)
        ->setOption($opt2);

        $ao14 = new OptionsAbonnement();
        $ao14->setPresence(true)
        ->setAbonnement($abonnement4)
        ->setOption($opt2);

        $ao21 = new OptionsAbonnement();
        $ao21->setPresence(false)
        ->setAbonnement($abonnement)
        ->setOption($opt3);

        $ao22 = new OptionsAbonnement();
        $ao22->setPresence(false)
        ->setAbonnement($abonnement2)
        ->setOption($opt3);

        $ao23 = new OptionsAbonnement();
        $ao23->setPresence(false)
        ->setAbonnement($abonnement3)
        ->setOption($opt3);

        $ao24 = new OptionsAbonnement();
        $ao24->setPresence(true)
        ->setAbonnement($abonnement4)
        ->setOption($opt3);

        
        $manager->persist($opt1);
        $manager->persist($opt2);
        $manager->persist($opt3);
        $manager->persist($abonnement);
        $manager->persist($abonnement2);
        $manager->persist($abonnement3);
        $manager->persist($abonnement4);
        $manager->persist($ao1);
        $manager->persist($ao2);
        $manager->persist($ao3);
        $manager->persist($ao4);
        $manager->persist($ao11);
        $manager->persist($ao12);
        $manager->persist($ao13);
        $manager->persist($ao14);
        $manager->persist($ao21);
        $manager->persist($ao22);
        $manager->persist($ao23);
        $manager->persist($ao24);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev', 'abodev'];
    }
}