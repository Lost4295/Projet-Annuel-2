<?php

namespace App\DataFixtures\Dev;

use App\Entity\AppartPlus;
use App\Entity\Location;
use App\Entity\Service;
use App\Service\AppartementService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Appartement;
use App\Entity\Note;
use DateTime;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppartementFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private AppartementService $as;
    public function __construct(AppartementService $as)
    {
        $this->as = $as;
    }

    public function load(ObjectManager $manager):void
    {
        $city = [ 0 => 'Paris', 1 => 'Madrid', 2 => 'Rome', 3 => 'Berlin', 4 => 'Londres'];
        $country = [ 0 => 'France', 1 => 'Espagne', 2 => 'Italie', 3 => 'Allemagne', 4 => 'Angleterre'];
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
        for ($j = 1; $j <= 40; $j++) {
            $that =$this->getReference('voyageur'.rand(1, 10).'-user');
            $nott = new Note();
            $nott->setService($this->getReference('service'.rand(1,20)));
            $nott->setUser($that);
            $nott->setNote(rand(1, 5));
            $manager->persist($nott);
        }
        for ($i =1 ; $i < 15; $i++){
            $appartement = new Appartement();
            $appartement->setAddress($i.' rue du bailleur');
            $appartement->setTitre('Appartement F2 au '.$i.'ème étage');
            $appartement->setPostalCode(sprintf("750%02d", $i));
            $appartement->setDescription('Appartement de type F2, situé au '.$i.'ème étage d\'un immeuble de 15 étages. Il est composé d\'un séjour, d\'une cuisine, d\'une chambre, d\'une salle de bain et d\'un WC. Il est équipé d\'un chauffage individuel électrique. L\'appartement est situé à proximité des commerces et des transports en commun. Il est disponible immédiatement.');
            $appartement->setShortDesc("Appartement de type F2, situé au ".$i."ème étage d'un immeuble de 15 étages.");
            $appartement->setCity($city[rand(0, 4)]);
            $appartement->setCountry($country[rand(0, 4)]);
            $appartement->setSurface(50);
            $appartement->setNbVoyageurs(rand(5,15));
            $appartement->setNbchambers($i);
            $appartement->setBailleur($this->getReference('bailleurp'.rand(1,6).'-user'));
            $appartement->setNbBeds(rand(1,5));
            $appartement->setNbBathrooms(rand(1,3));
            $appartement->setPrice(rand(50, 200));
            $appartement->setCreatedAt(new \DateTime(sprintf('202%d-01-%02d',rand(2,9), rand(1,31))));
            $appartement->setUpdatedAt(new \DateTime(sprintf('202%d-01-%02d',rand(2,9), rand(1,31))));
            $this->addReference('appartement'.$i, $appartement);
            $manager->persist($appartement);
        }
        for ($j = 1; $j <= 6; $j++) {
            $plusie = new AppartPlus();
            $plusie->setIcon($j);
            for ($i = 1; $i <= rand(1,6); $i++) {
                $plusie->addAppartement($this->getReference('appartement'.rand(1,14)));
            }
            $manager->persist($plusie);
        }
        for ($i = 1 ; $i < 40 ; $i++){
            $that =$this->getReference('voyageur'.rand(1, 10).'-user');
            $loca = new Location();
            $loca->setLocataire($that);
            $num = rand(1, 29);
            $y = rand(0, 9);
            $z = rand(1,12);
            $date = new DateTime(sprintf('202%d-%02d-%02d', $y, $z, $num));
            $loca->setDateDebut($date);
            $date2 = new DateTime(sprintf('202%d-%02d-%02d', $y, $z, rand($num + 1, 31)));
            $loca->setDateFin($date2);
            $loca->setAppartement($this->getReference('appartement'.rand(1, 14)));
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
            $days = $date->diff($date2)->days;
            $loca->setPrice($loca->getAppartement()->getPrice()*$days +$price);
            $this->addReference('location'.$i, $loca);
            for ($j = 1; $j <= 40; $j++) {
                $nott = new Note();
                $nott->setLocation($this->getReference('location'.$i));
                $nott->setUser($that);
                $nott->setNote(rand(1, 5));
                $manager->persist($nott);
            }
            $manager->persist($loca);
        }
        $manager->flush();
        for ($i = 1 ; $i < 15 ; $i++){
            $appartement = $this->getReference('appartement'.$i);
            $this->as->updateAppart($appartement->getId());
        }
    }
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
    public static function getGroups(): array
    {
        return ['dev', 'appartdev'];
    }
}