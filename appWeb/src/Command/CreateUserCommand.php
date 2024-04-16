<?php

// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:create-user', description: 'Creates a new user.', aliases: ['create:user', 'c:u'])]
class CreateUserCommand extends Command
{

    
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hash
    ){
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            // the command description shown when running "php bin/console list"
            ->setDescription('Creates a new user.')
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to create a user...')
            ->addArgument("name", InputArgument::REQUIRED, "The name of the user")
            ->addArgument("lastname", InputArgument::REQUIRED, "The lastname of the user")
            ->addArgument("email", InputArgument::REQUIRED, "The email of the user")
            ->addArgument("password", InputArgument::REQUIRED, "The password of the user")
            ->addArgument("phonenumber", InputArgument::REQUIRED, "The phonenumber of the user")
            ->addOption('isAdmin', "a", InputOption::VALUE_NONE, 'Is the user created an admin?')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Create User Command Interactive Wizard');
        $io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:create-user name lastname email password phonenumber',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // Ask the user for the name if it's not defined
        if (!$input->getArgument('name')) {
            $name = $io->ask('Name', 'name');
            $input->setArgument('name', $name);
        }


        // Ask the user for the lastname if it's not defined
        if (!$input->getArgument('lastname')) {
            $lastname = $io->ask('Lastname', 'lastname');
            $input->setArgument('lastname', $lastname);
        }



        // Ask the user for the email if it's not defined
        while (!$input->getArgument('email') && !filter_var($input->getArgument('email'), FILTER_VALIDATE_EMAIL)) {
            $email = $io->ask('Email');
            $input->setArgument('email', $email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $io->error('Invalid email');
            }
        }

        // Ask the user for the password if it's not defined
        if (!$input->getArgument('password')) {
            $password = $io->ask('Password', 'password');
            $input->setArgument('password', $password);
        }

        // Ask the user for the phonenumber if it's not defined
        if (!$input->getArgument('phonenumber')) {
            $phonenumber = $io->ask('Phonenumber', '0101010201');
            $input->setArgument('phonenumber', $phonenumber);
        }

        // Ask the user if the user is an admin
        if (!$input->getOption('isAdmin')) {
            $isAdmin = $io->confirm('Is the user an admin?', false);
            $input->setOption('isAdmin', $isAdmin);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // ... put here the code to create the user

        $io = new SymfonyStyle($input, $output);

        $io->title('Create User Command Result');
        $io->text([
            'Email: '.$input->getArgument('email'),
            'Password: '.$input->getArgument('password'),
            'Name: '.$input->getArgument('name'),
            'Lastname: '.$input->getArgument('lastname'),
            'Phonenumber: '.$input->getArgument('phonenumber'),
            'Is Admin: '.$input->getOption('isAdmin') ? 'Yes' : 'No',
        ]);

        $em = $this->em;
        $hash= $this->hash;
        $user = new User();
        $user->setEmail($input->getArgument('email'));
        $user->setPassword($hash->hashPassword($user, $input->getArgument('password')));
        $user->setNom($input->getArgument('name'));
        $user->setPrenom($input->getArgument('lastname'));
        $user->setPhonenumber($input->getArgument('phonenumber'));
        $em->persist($user);
        $em->flush();

        $io->success('User successfully generated!');






        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable
        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}