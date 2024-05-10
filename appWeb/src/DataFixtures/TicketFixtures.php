<?php

namespace App\DataFixtures;

use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TicketFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 5; $i++) {
            $ticket = new Ticket();
            $ticket->setTitre('Ticket ' . $i);
            $ticket->setDescription('Description du ticket ' . $i);
            $ticket->setDateOuverture(new \DateTime('2021-01-0' . $i));
            $ticket->setDateFermeture(new \DateTime('2021-01-0' . $i+1));
            $ticket->setResolveur($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
            $ticket->setDemandeur($this->getReference(UserFixtures::SUP_ADMIN_USER_REFERENCE));
            $ticket->setStatus();

            
            $manager->persist($ticket);
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