<?php

namespace App\DataFixtures;

use App\Entity\Professionnel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $encoder;
    public const ADMIN_USER_REFERENCE = 'bailleur-user';

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager): void
    {
        $voyageur = new User();
        $voyageur->setEmail('voyageur@pcs.fr')
            ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
            ->setRoles([User::ROLE_USER, User::ROLE_VOYAGEUR])
            ->setPassword($this->encoder->hashPassword($voyageur, 'voyageur'))
            ->setNom('Voyageur')
            ->setPrenom('Voyageur')
            ->setPhoneNumber('0606060606')
            ->setBirthdate(new \DateTime('1990-01-01'))
            ->setIsVerified(true);
        $manager->persist($voyageur);
        $bailleur = new User();
        $bailleur->setEmail('bailleur@pcs.fr')
            ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
            ->setRoles([User::ROLE_USER, User::ROLE_BAILLEUR])
            ->setPassword($this->encoder->hashPassword($bailleur, 'bailleur'))
            ->setNom('Bailleur')
            ->setPrenom('Bailleur')
            ->setPhoneNumber('0606060606')
            ->setBirthdate(new \DateTime('1990-01-01'))
            ->setIsVerified(true);
        $bailleurpro = new Professionnel();
        $bailleurpro->setResponsable($bailleur);
        $bailleurpro->setSiretNumber("2345678912345");
        $bailleurpro->setSocietyName('BailleurPro');
        $bailleurpro->setSocietyAddress('1 rue du bailleur');
        $bailleurpro->setPostalCode('75000');
        $bailleurpro->setCity('Paris');
        $bailleurpro->setCountry('France');
        $this->addReference(self::ADMIN_USER_REFERENCE, $bailleurpro);

        $manager->persist($bailleurpro);
        $manager->persist($bailleur);
        $admin = new User();
        $admin->setEmail('admin@pcs.fr')
            ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
            ->setRoles([User::ROLE_ADMIN, User::ROLE_USER,])
            ->setPassword($this->encoder->hashPassword($admin, 'admin'))
            ->setNom('Admin')
            ->setPrenom('Admin')
            ->setPhoneNumber('0606060606')
            ->setBirthdate(new \DateTime('1990-01-01'))
            ->setIsVerified(true);
        $manager->persist($admin);
        $prestaire = new User();
        $prestaire->setEmail('presta@pcs.fr')
            ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
            ->setRoles([User::ROLE_USER, User::ROLE_PRESTA])
            ->setPassword($this->encoder->hashPassword($prestaire, 'presta'))
            ->setNom('Prestataire')
            ->setPrenom('Prestataire')
            ->setPhoneNumber('0606060606')
            ->setBirthdate(new \DateTime('1990-01-01'))
            ->setIsVerified(true);
        $manager->persist($prestaire);
        $prestapro = new Professionnel();
        $prestapro->setResponsable($prestaire);
        $prestapro->setSiretNumber("2345678912345");
        $prestapro->setSocietyName('PrestaPro');
        $prestapro->setSocietyAddress('1 rue du prestataire');
        $prestapro->setPostalCode('75000');
        $prestapro->setCity('Paris');
        $prestapro->setCountry('France');
        $manager->persist($prestapro);

        $api = new User();
        $api->setEmail('api@pcs.fr')
            ->setAbonnement($this->getReference(AbonnementFixtures::GRATUIT))
            ->setRoles([User::ROLE_NON_USER])
            ->setPassword($this->encoder->hashPassword($api, 'api'))
            ->setNom('Api')
            ->setPrenom('Api')
            ->setPhoneNumber('0606060606')
            ->setBirthdate(new \DateTime('1990-01-01'))
            ->setIsVerified(true);
        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            AbonnementFixtures::class,
        ];
    }
}
