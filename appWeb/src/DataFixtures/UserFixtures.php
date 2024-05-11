<?php

namespace App\DataFixtures;

use App\Entity\Professionnel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private $encoder;



    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function rander(int $length, bool $number = true, bool $letter = true, bool $special = true): string
    {
        $number = '0123456789';
        $letter = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $special = '!@#$%^&*()_+{}|:<>?';
        $characters = '';
        $characters .= $number ? $number : '';
        $characters .= $letter ? $letter : '';
        $characters .= $special ? $special : '';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $voyageur = new User();
            $voyageur->setEmail("voyageur$i@pcs.fr")
                ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
                ->setRoles([User::ROLE_USER, User::ROLE_VOYAGEUR])
                ->setPassword($this->encoder->hashPassword($voyageur, 'voyageur'))
                ->setNom("Voyageur $i")
                ->setPrenom(self::rander(8, false, true, false))
                ->setPhoneNumber('06' . self::rander(8, true, false, false))
                ->setBirthdate(new \DateTime('1990-01-01'))
                ->setIsVerified(true);
            $this->setReference("voyageur$i-user", $voyageur);
            $manager->persist($voyageur);
        }
        for ($i = 1; $i <= 6; $i++) {
            $bailleur = new User();
            $bailleur->setEmail("bailleur$i@pcs.fr")
                ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
                ->setRoles([User::ROLE_USER, User::ROLE_BAILLEUR])
                ->setPassword($this->encoder->hashPassword($bailleur, "bailleur"))
                ->setNom("Bailleur $i")
                ->setPrenom(self::rander(8, false, true, false))
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
            $manager->persist($bailleurpro);
            $manager->persist($bailleur);
        }

        $admin = new User();
        $admin->setEmail("admin@pcs.fr")
            ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
            ->setRoles([User::ROLE_ADMIN, User::ROLE_USER])
            ->setPassword($this->encoder->hashPassword($admin, "admin"))
            ->setNom("Admin")
            ->setPrenom("Admin")
            ->setPhoneNumber("06" . self::rander(8, true, false, false))
            ->setBirthdate(new \DateTime(sprintf("19%02d-01-01", $i)))
            ->setIsVerified(true);
        $this->addReference("admin-user", $admin);
        $manager->persist($admin);
        for ($i = 1; $i <= 15; $i++) {
            $prestaire = new User();
            $prestaire->setEmail("presta$i@pcs.fr")
                ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
                ->setRoles([User::ROLE_USER, User::ROLE_PRESTA])
                ->setPassword($this->encoder->hashPassword($prestaire, 'presta'))
                ->setNom("Prestataire $i")
                ->setPrenom(self::rander(8, false, true, false))
                ->setPhoneNumber("06" . self::rander(8, true, false, false))
                ->setBirthdate(new \DateTime(sprintf("19%02d-01-01", $i)))
                ->setIsVerified(true);
            $prestapro = new Professionnel();
            $prestapro->setResponsable($prestaire);
            $prestapro->setSiretNumber(self::rander(14, true, false, false));
            $prestapro->setSocietyName("PrestaPro $i");
            $prestapro->setSocietyAddress("$i rue du prestataire");
            $prestapro->setPostalCode(self::rander(5, true, false, false));
            $prestapro->setCity("Paris");
            $prestapro->setCountry("France");
            $this->addReference("presta$i-user", $prestapro);
            $manager->persist($prestaire);
            $manager->persist($prestapro);
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
}
