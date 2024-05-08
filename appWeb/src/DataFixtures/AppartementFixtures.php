<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Appartement;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppartementFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager):void
    {
        for ($i =1 ; $i< 10; $i++){

            $appartement = new Appartement();
            $appartement->setAddress($i.' rue du bailleur');
            $appartement->setTitre('Appartement F2 au '.$i.'ème étage');
            $appartement->setPostalCode("750".$i."0");
            $appartement->setDescription('Appartement de type F2, situé au '.$i.'ème étage d\'un immeuble de 5 étages. Il est composé d\'un séjour, d\'une cuisine, d\'une chambre, d\'une salle de bain et d\'un WC. Il est équipé d\'un chauffage individuel électrique. L\'appartement est situé à proximité des commerces et des transports en commun. Il est disponible immédiatement.');
            $appartement->setShortDesc("Appartement de type F2, situé au ".$i."ème étage d'un immeuble de 5 étages.");
            $appartement->setCity('Paris');
            $appartement->setCountry('France');
            $appartement->setSurface(50);
            $appartement->setNbVoyageurs(2);
            $appartement->setNbchambers($i);
            $appartement->setBailleur($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
            $appartement->setNbBeds(1);
            $appartement->setNbBathrooms(1);
            $appartement->setPrice(1000);
            $appartement->setCreatedAt(new \DateTime('2021-01-0'.$i));
            $appartement->setUpdatedAt(new \DateTime('2021-01-0'.$i));
            $manager->persist($appartement);
        } 
            $manager->flush();
    }
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}