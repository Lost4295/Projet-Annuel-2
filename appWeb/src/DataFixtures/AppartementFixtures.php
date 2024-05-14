<?php

namespace App\DataFixtures;

use App\Entity\AppartPlus;
use App\Entity\Location;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Appartement;
use App\Entity\Note;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppartementFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager):void
    {
        for ($i = 1 ; $i < 21 ; $i++){
            $serv= new Service();
            $serv->setPrestataire($this->getReference('presta'.rand(1, 15).'-user'));
            $serv->setTitre('Service '.$i);
            $serv->setTarifs(rand(1,100));
            $serv->setType(rand(1,6));
            $serv->setDescription('Description du service '.$i);
            $this->addReference('service'.$i, $serv);
            $manager->persist($serv);
        }
        for ($i =1 ; $i < 7; $i++){
            $appartement = new Appartement();
            $appartement->setAddress($i.' rue du bailleur');
            $appartement->setTitre('Appartement F2 au '.$i.'ème étage');
            $appartement->setPostalCode("750".$i."0");
            $appartement->setDescription('Appartement de type F2, situé au '.$i.'ème étage d\'un immeuble de 5 étages. Il est composé d\'un séjour, d\'une cuisine, d\'une chambre, d\'une salle de bain et d\'un WC. Il est équipé d\'un chauffage individuel électrique. L\'appartement est situé à proximité des commerces et des transports en commun. Il est disponible immédiatement.');
            $appartement->setShortDesc("Appartement de type F2, situé au ".$i."ème étage d'un immeuble de 5 étages.");
            $appartement->setCity('Paris');
            $appartement->setCountry('France');
            $appartement->setSurface(50);
            $appartement->setNbVoyageurs(rand(5,15));
            $appartement->setNbchambers($i);
            $appartement->setBailleur($this->getReference('bailleurp'.$i.'-user'));
            $appartement->setNbBeds(rand(1,5));
            $appartement->setNbBathrooms(rand(1,3));
            $appartement->setPrice(rand(50, 200));
            $appartement->setCreatedAt(new \DateTime('2021-01-0'.$i));
            $appartement->setUpdatedAt(new \DateTime('2021-01-0'.$i));
            $this->addReference('appartement'.$i, $appartement);
            $manager->persist($appartement);
            for ($j = 1; $j <= rand(1,6); $j++) {
                $plusie = new AppartPlus();
                $plusie->setIcon(rand(1, 6));
                $plusie->addAppartement($appartement);
                $manager->persist($plusie);
            }
        }
        for ($i = 1 ; $i < 11 ; $i++){
            $that =$this->getReference('voyageur'.rand(1, 10).'-user');
            $loca = new Location();
            $loca->setLocataire($that);
            $date = new \DateTime(sprintf('202%d-01-%02d',rand(2,9), $i));
            $loca->setDateDebut($date);
            $loca->setDateFin($date->add(new \DateInterval('P'.rand(1, 10).'D')));
            $loca->setAppartement($this->getReference('appartement'.rand(1, 6)));
            $loca->setAdults(rand(1, 4));
            $loca->setKids(rand(0, 2));
            $loca->setBabies(rand(0, 1));
            for ($j = 1; $j <= rand(1, 10); $j++) {
                $loca->addService($this->getReference('service'.rand(1, 20)));
            }
            $price = 0;
            foreach ($loca->getServices() as $service) {
                $price += $service->getTarifs();
            }
            $days = $loca->getDateDebut()->diff($loca->getDateFin())->days;
            $loca->setPrice($loca->getAppartement()->getPrice()*$days +$price);
            $this->addReference('location'.$i, $loca);
            for ($j = 1; $j <= 11; $j++) {
                $nott = new Note();
                $nott->setLocation($this->getReference('location'.$i));
                $nott->setUser($that);
                $nott->setNote(rand(1, 5));
                $manager->persist($nott);
            }
            $manager->persist($loca);
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