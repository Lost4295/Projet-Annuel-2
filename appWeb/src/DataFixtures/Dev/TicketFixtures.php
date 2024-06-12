<?php

namespace App\DataFixtures\Dev;


use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TicketFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $ticket = new Ticket();
            $ticket->setTitre('Ticket ' . $i);
            $ticket->setDescription('Description du ticket ' . $i);
            $ticket->setDateOuverture(new \DateTime('2021-01-0' . $i));
            $ticket->setDateFermeture(new \DateTime('2021-02-0' . $i+1));
            $ticket->setResolveur($this->getReference("admin-user"));
            $ticket->setDemandeur($this->getReference('bailleur' . $i . '-user'));
            $ticket->setStatus(rand(1, 6));
            $ticket->setType(rand(1, 5));
            $ticket->setCategory(rand(1, 5));
            $ticket->setPriority(rand(1, 4));
            $ticket->setUrgence(rand(1, 4));
            $ticket->setLastUpdateDate(new \DateTime('2021-01-0' . $i+1));
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
    public static function getGroups(): array
    {
        return ['dev', 'ticketdev'];
    }
}
