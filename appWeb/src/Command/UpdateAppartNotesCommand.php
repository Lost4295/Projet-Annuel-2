<?php

namespace App\Command;

use App\Controller\AjaxController;
use App\Entity\Appartement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-notes',
    description: 'Add a short description for your command',
    aliases: ['a:u:n'],
)]
class UpdateAppartNotesCommand extends Command
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $apparts = $this->em->getRepository(Appartement::class)->findAll();
        $ac = new AjaxController($this->em);
        foreach ($apparts as $appart) {
            $ac->updateAppart($appart->getId());
        }
        $io->success('Notes updated successfully!');

        return Command::SUCCESS;
    }
}
