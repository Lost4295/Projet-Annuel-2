<?php

namespace App\Command;

use App\Entity\Fichier;
use App\Entity\Location;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-invoices',
    description: 'Add a short description for your command',
)]
class CreateInvoicesCommand extends Command
{
    private $pdfService;
    private $em;
    public function __construct(PdfService $pdfService, EntityManagerInterface $em)
    {
        $this->pdfService = $pdfService;
        $this->em = $em;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pct = 0;
        $los = $this->em->getRepository(Location::class)->findBy(["factToCreate" => true]);
        foreach ($los as $loc){
            $loc->setFactToCreate(false);
            $file = new Fichier();
            $file->setLocation($loc);
            $file->setDate(new \DateTime());
            $file->setUser($loc->getLocataire());
            $file->setNom("Facture de location du " . $file->getDate()->format('d/m/Y'));
            $file->setType('location');
            $fac = $this->pdfService->generatePdf($loc);
            if (file_exists($fac[0])) {
                $file->setSize(PdfService::human_filesize(filesize($fac[0])));
                $file->setPath($fac[1]);
            }
            $loc->setFacture($file);
            $this->em->persist($loc);
            $this->em->persist($file);
            $this->em->flush();
            $pct++;
        }



        $io->success($pct.' new invoices created.');

        return Command::SUCCESS;
    }
}
