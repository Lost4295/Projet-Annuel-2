<?php

namespace App\DataFixtures\Prod;

use App\Entity\Devis;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DevisFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
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
        for ($i = 1 ; $i < 21 ; $i++){
            $devis = new Devis();
            $user = $this->getReference('bailleur'.rand(1,6).'-user');
            $devis->setEmail($user->getEmail());
            $devis->setNom($user->getNom());
            $devis->setPrenom($user->getPrenom());
            $devis->setNumero($user->getPhoneNumber());
            $devis->setTypePresta(rand(1,6));
            $manager->persist($devis);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
    public static function getGroups(): array
    {
        return ['prod', 'devisprod'];
    }
}
