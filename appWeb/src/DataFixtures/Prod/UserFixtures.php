<?php

namespace App\DataFixtures\Prod;

use App\Entity\Fichier;
use App\Entity\Note;
use App\Entity\Professionnel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private $encoder;

    private $people = [
        ["first_name" => "Jean", "last_name" => "Dupont"],
        ["first_name" => "Marie", "last_name" => "Martin"],
        ["first_name" => "Pierre", "last_name" => "Bernard"],
        ["first_name" => "Lucie", "last_name" => "Dubois"],
        ["first_name" => "Paul", "last_name" => "Lefevre"],
        ["first_name" => "Sophie", "last_name" => "Moreau"],
        ["first_name" => "Henri", "last_name" => "Simon"],
        ["first_name" => "Claire", "last_name" => "Laurent"],
        ["first_name" => "Julien", "last_name" => "Michel"],
        ["first_name" => "Nathalie", "last_name" => "Garcia"],
        ["first_name" => "Marc", "last_name" => "Thomas"],
        ["first_name" => "Isabelle", "last_name" => "Rousseau"],
        ["first_name" => "Thomas", "last_name" => "Petit"],
        ["first_name" => "Amélie", "last_name" => "Durand"],
        ["first_name" => "Antoine", "last_name" => "Lemoine"],
        ["first_name" => "Elodie", "last_name" => "Germain"],
        ["first_name" => "Hugo", "last_name" => "Perrin"],
        ["first_name" => "Julie", "last_name" => "Fournier"],
        ["first_name" => "Louis", "last_name" => "Morin"],
        ["first_name" => "Camille", "last_name" => "Garnier"],
        ["first_name" => "Victor", "last_name" => "Renaud"],
        ["first_name" => "Alice", "last_name" => "Blanc"],
        ["first_name" => "Maxime", "last_name" => "Faure"],
        ["first_name" => "Emilie", "last_name" => "Joly"],
        ["first_name" => "Etienne", "last_name" => "Poulain"],
        ["first_name" => "Charlotte", "last_name" => "Rodriguez"],
        ["first_name" => "Gilles", "last_name" => "Legrand"],
        ["first_name" => "Sandrine", "last_name" => "Martinez"],
        ["first_name" => "Laurent", "last_name" => "Dupuis"],
        ["first_name" => "Corinne", "last_name" => "Renard"],
        ["first_name" => "Sebastien", "last_name" => "Leclerc"],
        ["first_name" => "Amandine", "last_name" => "Bourgeois"]
    ];

    private $society = [
        "Tech Innovators",
        "Green Solutions",
        "Élite Construction",
        "Cuisine Délicieuse",
        "Nettoyage Pro",
        "Fitness Vitalité",
        "Événements Magnifiques",
        "Consultants Financiers",
        "Développement Web",
        "Marketing Créatif",
        "Réparations Rapides",
        "Jardinage Expert",
        "Photographie Éclatante",
        "Transport Fiable",
        "Sécurité Maximale"
    ];

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function rander(int $length, bool $number = true, bool $letter = true, bool $special = true): string
    {
        $numbere = '0123456789';
        $lettere = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $speciale = '!@#$%^&*()_+{}|:<>?';
        $characters = '';
        $characters .= $number ? $numbere : '';
        $characters .= $letter ? $lettere : '';
        $characters .= $special ? $speciale : '';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function load(ObjectManager $manager): void 
    {
        
        $cpt = 0;
        for ($i = 1; $i <= 10; $i++) {
            $voyageur = new User();
            $voyageur->setEmail("voyageur$i@pcs.fr")
                ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
                ->setRoles([User::ROLE_USER, User::ROLE_VOYAGEUR])
                ->setPassword($this->encoder->hashPassword($voyageur, 'voyageur'))
                ->setNom($this->people[$cpt]['last_name'])
                ->setPrenom($this->people[$cpt]['first_name'])
                ->setPhoneNumber('06' . self::rander(8, true, false, false))
                ->setBirthdate(new \DateTime('1990-01-01'))
                ->setIsVerified(true);
            $this->setReference("voyageur$i-user", $voyageur);
            $file = new Fichier();
            $file->setNom('test')
                ->setType('pdf')
                ->setPath('Bonjour.pdf')
                ->setSize(self::human_filesize(filesize(__DIR__.'/../../../public/files/pdfs/Bonjour.pdf')))
                ->setUser($this->getReference("voyageur$i-user"));
            $manager->persist($file);
            $manager->persist($voyageur);
            $cpt++;
            }
            for ($i = 1; $i <= 6; $i++) {
            $bailleur = new User();
            $bailleur->setEmail("bailleur$i@pcs.fr")
                ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
                ->setRoles([User::ROLE_USER, User::ROLE_BAILLEUR])
                ->setPassword($this->encoder->hashPassword($bailleur, "bailleur"))
                ->setNom($this->people[$cpt]['last_name'])
                ->setPrenom($this->people[$cpt]['first_name'])
                ->setPhoneNumber("06" . self::rander(8, true, false, false))
                ->setBirthdate(new \DateTime(sprintf("19%02d-01-01", $i)))
                ->setIsVerified(true);
                $bailleurpro = new Professionnel();
                $bailleurpro->setResponsable($bailleur);
                $bailleurpro->setSiretNumber(self::rander(14, true, false, false));
                $bailleurpro->setSocietyName("BailleurPro");
                $bailleurpro->setSocietyAddress("1 rue du bailleur");
                $bailleurpro->setPostalCode(self::rander(5, true, false, false));
                $bailleurpro->setCity("Paris");
                $bailleurpro->setCountry("France");
                $this->addReference("bailleur$i-user", $bailleur);
                $this->addReference("bailleurp$i-user", $bailleurpro);
                $file = new Fichier();
                $file->setNom('test')
                ->setType('pdf')
                ->setPath('Bonjour.pdf')
                ->setSize(self::human_filesize(filesize(__DIR__.'/../../../public/files/pdfs/Bonjour.pdf')))
                ->setUser($this->getReference("bailleur$i-user"));
                $manager->persist($file);
                $manager->persist($bailleurpro);
                $manager->persist($bailleur);
                $cpt++;
                }
                
        $admin = new User();
        $admin->setEmail("admin@pcs.fr")
            ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
            ->setRoles([User::ROLE_ADMIN, User::ROLE_USER])
            ->setPassword($this->encoder->hashPassword($admin, "admin"))
            ->setNom($this->people[$cpt]['last_name'])
            ->setPrenom($this->people[$cpt]['first_name'])
            ->setPhoneNumber("06" . self::rander(8, true, false, false))
            ->setBirthdate(new \DateTime(sprintf("19%02d-01-01", $i)))
            ->setIsVerified(true);
            $this->addReference("admin-user", $admin);
            $manager->persist($admin);
            $cpt++;
        for ($i = 1; $i <= 15; $i++) {
            $prestaire = new User();
            $prestaire->setEmail("presta$i@pcs.fr")
                ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
                ->setRoles([User::ROLE_USER, User::ROLE_PRESTA])
                ->setPassword($this->encoder->hashPassword($prestaire, 'presta'))
                ->setNom($this->people[$cpt]['last_name'])
                ->setPrenom($this->people[$cpt]['first_name'])
                ->setPhoneNumber("06" . self::rander(8, true, false, false))
                ->setBirthdate(new \DateTime(sprintf("19%02d-01-01", $i)))
                ->setIsVerified(true);
            $this->addReference("prestau$i-user", $prestaire);
            $prestapro = new Professionnel();
            $prestapro->setResponsable($prestaire);
            $prestapro->setSiretNumber(self::rander(14, true, false, false));
            $prestapro->setSocietyName($this->society[$i-1]);
            $prestapro->setSocietyAddress("$i rue du prestataire");
            $prestapro->setPostalCode("750" . rand(1, 20));
            $prestapro->setCity("Paris");
            $prestapro->setCountry("France");
            $this->addReference("presta$i-user", $prestapro);
            $file = new Fichier();
            $file->setNom('test')
                ->setType('pdf')
                ->setPath('Bonjour.pdf')
                ->setSize(self::human_filesize(filesize(__DIR__.'/../../../public/files/pdfs/Bonjour.pdf')))
                ->setUser($this->getReference("prestau$i-user"));
            $manager->persist($file);
            $manager->persist($prestaire);
            $manager->persist($prestapro);
            $cpt++;
        }

        $api = new User();
        $api->setEmail('api@pcs.fr')
            ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
            ->setRoles([User::ROLE_NON_USER])
            ->setPassword($this->encoder->hashPassword($api, 'api'))
            ->setNom('Api')
            ->setPrenom('Api')
            ->setPhoneNumber('06' . self::rander(8, true, false, false))
            ->setBirthdate(new \DateTime('1990-01-01'))
            ->setIsVerified(true);
        $manager->persist($api);
        $manager->flush();

    }
    public function getDependencies()
    {
        return [
            AbonnementFixtures::class,
        ];
    }

    function human_filesize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    public static function getGroups(): array
    {
        return ['prod', 'userprod'];
    }
}
