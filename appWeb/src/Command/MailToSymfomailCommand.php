<?php

namespace App\Command;

use App\Entity\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;

#[AsCommand(
    name: 'app:mail-to-symfomail',
    description: 'Add a short description for your command',
)]
class MailToSymfomailCommand extends Command
{
    private $em;
    private $mailer;
    public function __construct(EntityManagerInterface $em, MailerInterface $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
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

        $mails = $this->em->getRepository(Email::class)->findAll();
        $cpt = 0;
        foreach ($mails as $mail) {
            if ($mail->getDate() > new \DateTime()) {
                continue;
            }
            $cpt++;
            $email = (new \Symfony\Component\Mime\Email())
                ->from("mailer@kfjrf.fr")
                ->to($mail->getDestinataire())
                ->subject($mail->getObject())
                ->text($mail->getBody())
                ->html($mail->getBody())
            ;
            $this->mailer->send($email);
        }

        if ($cpt === 0) {
            $io->success('No email to send');
        } else {
            $io->success('Emails sent: '.$cpt);
        }

        return Command::SUCCESS;
    }
}
