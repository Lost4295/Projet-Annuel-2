<?php

namespace App\Command;

use App\Entity\Fichier;
use App\Entity\Professionnel;
use App\Entity\User;
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
    name: 'app:generate:factures',
    description: 'Add a short description for your command',
    aliases: ['a:g:f']
)]
class GenerateFacturesCommand extends Command
{
    private $pdf;
    private $em;
    public function __construct(EntityManagerInterface $em, PdfService $pdf)
    {
        $this->pdf = $pdf;
        $this->em = $em;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Start generating factures');

        // Get all clients
        $clients = $this->em->getRepository(User::class)->findAll();

        foreach ($clients as $client) {
            if ($client->hasRole("ROLE_VOYAGEUR")) {
                $data = $this->pdf->generateMonthlyPdf($client->getLocations()->toArray(), $client);
                $file = new Fichier();
                $date = new \DateTime();
                $file->setDate($date);
                $file->setNom("Factures du mois " . $date->format('m/Y'));
                $file->setType('location');
                if (file_exists($data[0])) {
                    $file->setSize(PdfService::human_filesize(filesize($data[0])));
                    $file->setPath($data[1]);
                }
                $file->setUser($client);
                $this->em->persist($file);
            } else {
                $resp = $this->em->getRepository(Professionnel::class)->findOneBy(['responsable' => $client]);
                $data = $this->pdf->generateMonthlyDevisPdf($resp->getDevis()->toArray(), $client);
                $file = new Fichier();
                $date = new \DateTime();
                $file->setDate($date);
                $file->setNom("Devis du mois " . $date->format('m/Y'));
                $file->setType('devis');
                if (file_exists($data[0])) {
                    $file->setSize(PdfService::human_filesize(filesize($data[0])));
                    $file->setPath($data[1]);
                }
                    $file->setUser($client);
                    $this->em->persist($file);
            }
        }
        $this->em->flush();
        return Command::SUCCESS;
    }
}
