<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Appartement;

class AppartementFixtures extends Fixture
{
    public function load(ObjectManager $manager):void
    {
        for ($i =1 ; $i< 10; $i++){

            $appartement = new Appartement();
            $appartement->setAddress($i.' rue du bailleur');
            $appartement->setPostalCode("750".$i."0");
            $appartement->setCity('Paris');
            $appartement->setCountry('France');
            $appartement->setSurface(50);
            $appartement->setNbRooms(2);
            $appartement->setNbBeds(1);
            $appartement->setNbBathrooms(1);
            $appartement->setPrice(1000);
            $appartement->setCreatedAt(new \DateTime('2021-01-0'.$i));
            $appartement->setUpdatedAt(new \DateTime('2021-01-0'.$i));
            $manager->persist($appartement);
        } 
            $manager->flush();
    }
}