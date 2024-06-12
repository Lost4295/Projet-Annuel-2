<?php

namespace App\DataFixtures\Prod;

use App\Entity\AppartPlus;
use App\Entity\Location;
use App\Entity\Service;
use App\Service\AppartementService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Appartement;
use App\Entity\Commentaire;
use App\Entity\Note;
use DateTime;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppartementFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private AppartementService $as;
    public function __construct(AppartementService $as)
    {
        $this->as = $as;
    }
    private $locations = [
        ["city" => "Paris", "country" => "France", "postal_code" => "75000"],
        ["city" => "Marseille", "country" => "France", "postal_code" => "13000"],
        ["city" => "Lyon", "country" => "France", "postal_code" => "69000"],
        ["city" => "Toulouse", "country" => "France", "postal_code" => "31000"],
        ["city" => "Nice", "country" => "France", "postal_code" => "06000"],
        ["city" => "Nantes", "country" => "France", "postal_code" => "44000"],
        ["city" => "Strasbourg", "country" => "France", "postal_code" => "67000"],
        ["city" => "Montpellier", "country" => "France", "postal_code" => "34000"],
        ["city" => "Bordeaux", "country" => "France", "postal_code" => "33000"],
        ["city" => "Lille", "country" => "France", "postal_code" => "59000"],
        ["city" => "Brussels", "country" => "Belgium", "postal_code" => "1000"],
        ["city" => "Antwerp", "country" => "Belgium", "postal_code" => "2000"],
        ["city" => "Ghent", "country" => "Belgium", "postal_code" => "9000"],
        ["city" => "Luxembourg City", "country" => "Luxembourg", "postal_code" => "L-1000"],
        ["city" => "Esch-sur-Alzette", "country" => "Luxembourg", "postal_code" => "L-4001"],
        ["city" => "Zurich", "country" => "Switzerland", "postal_code" => "8000"],
        ["city" => "Geneva", "country" => "Switzerland", "postal_code" => "1200"],
        ["city" => "Basel", "country" => "Switzerland", "postal_code" => "4000"],
        ["city" => "Bern", "country" => "Switzerland", "postal_code" => "3000"],
        ["city" => "Monaco", "country" => "Monaco", "postal_code" => "98000"],
        ["city" => "Andorra la Vella", "country" => "Andorra", "postal_code" => "AD500"],
        ["city" => "Berlin", "country" => "Germany", "postal_code" => "10115"],
        ["city" => "Munich", "country" => "Germany", "postal_code" => "80331"],
        ["city" => "Frankfurt", "country" => "Germany", "postal_code" => "60311"],
        ["city" => "Stuttgart", "country" => "Germany", "postal_code" => "70173"],
        ["city" => "Madrid", "country" => "Spain", "postal_code" => "28001"],
        ["city" => "Barcelona", "country" => "Spain", "postal_code" => "08001"],
        ["city" => "Valencia", "country" => "Spain", "postal_code" => "46001"],
        ["city" => "Seville", "country" => "Spain", "postal_code" => "41001"],
        ["city" => "Milan", "country" => "Italy", "postal_code" => "20100"],
        ["city" => "Rome", "country" => "Italy", "postal_code" => "00100"],
        ["city" => "Turin", "country" => "Italy", "postal_code" => "10100"]
    ];
    private $comms = [
        "L'appartement était propre et bien situé.",
        "Nous avons passé un excellent séjour dans cet appartement.",
        "L'hôte était très accueillant et serviable.",
        "L'appartement était exactement comme décrit dans l'annonce.",
        "Nous avons apprécié la proximité des transports en commun.",
        "La vue depuis l'appartement était magnifique.",
        "L'appartement était spacieux et confortable.",
        "Le quartier était animé et plein de vie.",
        "Nous avons adoré la décoration de l'appartement.",
        "L'hôte nous a donné d'excellents conseils sur les endroits à visiter.",
        "La réservation et l'arrivée se sont déroulées sans problème.",
        "L'appartement était bien équipé avec tout ce dont nous avions besoin.",
        "Nous avons eu une expérience positive dans cet appartement.",
        "Le lit était très confortable.",
        "La cuisine était bien équipée pour cuisiner nos repas.",
        "Nous avons apprécié la flexibilité de l'horaire de départ.",
        "L'emplacement de l'appartement était idéal pour explorer la ville.",
        "Nous recommandons vivement cet appartement à d'autres voyageurs.",
        "L'hôte était réactif et facile à contacter.",
        "Nous avons eu un excellent rapport qualité-prix avec cet appartement.",
        "L'appartement était calme et paisible, parfait pour se reposer.",
        "Nous avons été impressionnés par la propreté de l'appartement.",
        "Nous avons passé un séjour très agréable dans cet appartement.",
        "L'hôte était sympathique et accueillant.",
        "Nous avons eu une expérience sans tracas avec cette location d'appartement.",
        "L'appartement était bien situé près des attractions touristiques.",
        "Nous avons été enchantés par la décoration moderne de l'appartement.",
        "Nous avons passé un excellent week-end dans cet appartement.",
        "Nous avons été satisfaits de notre choix d'appartement pour notre séjour.",
        "L'appartement était lumineux et bien aménagé.",
        "Nous avons apprécié la flexibilité de l'hôte concernant l'heure d'arrivée.",
        "L'appartement était propre et bien entretenu.",
        "L'hôte était attentionné et a répondu à toutes nos questions rapidement.",
        "Nous avons été agréablement surpris par la taille de l'appartement.",
        "L'emplacement de l'appartement était pratique pour se déplacer en ville.",
        "Nous avons passé un excellent séjour dans cet appartement confortable.",
        "L'hôte nous a donné de bons conseils sur les restaurants locaux.",
        "Nous avons adoré la vue depuis le balcon de l'appartement.",
        "L'appartement était bien équipé avec des équipements modernes.",
        "Nous avons été très satisfaits de notre expérience de location d'appartement.",
        "L'hôte était sympathique et arrangeant.",
        "Nous avons passé un séjour relaxant dans cet appartement.",
        "L'appartement était proche des commodités et des magasins.",
        "Nous avons été impressionnés par la propreté et l'organisation de l'appartement.",
        "Nous recommandons cet appartement à tous ceux qui visitent la région.",
        "L'hôte nous a accueillis chaleureusement à notre arrivée.",
        "L'appartement était bien situé pour explorer la ville à pied.",
        "Nous avons été ravis de la réactivité de l'hôte à nos demandes.",
        "Nous avons passé un séjour agréable dans cet appartement bien équipé.",
        "L'emplacement de l'appartement était central et pratique.",
        "Nous avons passé un excellent séjour et nous reviendrons certainement dans cet appartement.",
        "L'hôte a été très accommodant et a rendu notre séjour agréable.",
        "Nous avons été satisfaits de notre choix d'appartement pour nos vacances.",
        "L'appartement était propre mais assez bruyant.",
        "Nous avons eu quelques problèmes mineurs avec l'appartement.",
        "L'hôte n'était pas très disponible pendant notre séjour.",
        "L'appartement était un peu plus petit que ce à quoi nous nous attendions.",
        "La connexion Wi-Fi dans l'appartement était peu fiable.",
        "Nous avons trouvé l'emplacement de l'appartement un peu éloigné des attractions principales.",
        "Certains équipements dans l'appartement étaient un peu usés.",
        "Nous avons eu du mal à trouver un parking à proximité de l'appartement.",
        "La communication avec l'hôte était un peu difficile.",
        "L'appartement avait besoin d'un peu plus de nettoyage à notre arrivée.",
        "Nous avons été déçus par la qualité de certains meubles dans l'appartement.",
        "Certains voisins étaient bruyants pendant notre séjour.",
        "L'équipement de cuisine dans l'appartement était un peu limité.",
        "Nous avons trouvé l'appartement un peu sombre et mal éclairé.",
        "L'emplacement de l'appartement était un peu difficile à trouver.",
        "Nous avons eu du mal à régler la température dans l'appartement.",
        "L'appartement avait une odeur désagréable à notre arrivée.",
        "Nous avons trouvé l'appartement un peu impersonnel et sans charme.",
        "Certains équipements dans l'appartement étaient défectueux.",
        "L'appartement était un peu poussiéreux à notre arrivée.",
        "Nous avons été déçus par la propreté générale de l'appartement.",
        "L'hôte était un peu en retard pour notre enregistrement.",
        "L'appartement était situé dans un quartier un peu malfamé.",
        "Nous avons eu du mal à obtenir des réponses de l'hôte avant notre arrivée.",
        "Nous avons trouvé l'appartement un peu vieux et démodé.",
        "Certains équipements dans l'appartement étaient mal entretenus.",
        "L'appartement était un peu mal isolé du bruit extérieur.",
        "Nous avons eu quelques problèmes avec la plomberie dans l'appartement.",
        "L'appartement avait une ambiance un peu négligée.",
        "Nous avons été déçus par la vue depuis l'appartement.",
        "Certains équipements dans l'appartement étaient cassés.",
        "L'appartement avait une odeur de renfermé.",
        "Nous avons trouvé l'appartement un peu cher pour ce qu'il offrait.",
        "L'appartement était situé loin des transports en commun.",
        "Nous avons eu quelques problèmes avec l'électricité dans l'appartement.",
        "L'appartement était un peu sale à notre arrivée.",
        "Nous avons trouvé l'appartement un peu désordonné.",
        "Certains équipements dans l'appartement étaient obsolètes.",
        "L'appartement avait besoin de quelques réparations mineures.",
        "Nous avons été déçus par la qualité du lit dans l'appartement.",
        "L'hôte n'était pas très réactif à nos demandes.",
        "Nous avons trouvé l'appartement un peu inconfortable.",
        "Certains voisins étaient bruyants pendant la nuit.",
        "L'appartement était un peu malodorant.",
        "Nous avons été déçus par la propreté de la salle de bains dans l'appartement.",
        "L'appartement était un peu humide.",
        "Nous avons trouvé l'appartement un peu étroit pour notre famille.",
        "Certains équipements dans l'appartement étaient en panne.",
        "L'appartement était situé dans un quartier un peu sale.",
    ];

    private $services = [
        ["service" => "Installation de bouées", "description" => "Installation et maintenance de systèmes bouétiques.", "type" => Service::NETTOYAGE],
        ["service" => "Installation de systèmes informatiques", "description" => "Installation et maintenance de systèmes informatiques.", "type" => Service::ELECTRICITE],
        ["service" => "Conseils en développement durable", "description" => "Conseils en développement durable pour entreprises.", "type" => Service::PEINTURE],
        ["service" => "Construction de bâtiments", "description" => "Construction de maisons et bâtiments commerciaux.", "type" => Service::CHAUFFEUR],
        ["service" => "Traiteur pour événements", "description" => "Service de traiteur pour événements privés et professionnels.", "type" => Service::BRICOLAGE],
        ["service" => "Nettoyage professionnel", "description" => "Nettoyage professionnel de bureaux et résidences.", "type" => Service::PLOMBERIE],
        ["service" => "Coaching en fitness", "description" => "Coaching personnalisé en fitness et nutrition.", "type" => Service::NETTOYAGE],
        ["service" => "Organisation d'événements", "description" => "Organisation d'événements de mariage et fêtes.", "type" => Service::BRICOLAGE],
        ["service" => "Consultation financière", "description" => "Consultation financière et planification d'investissements.", "type" => Service::ELECTRICITE],
        ["service" => "Développement web", "description" => "Développement de sites web et applications mobiles.", "type" => Service::PLOMBERIE],
        ["service" => "Marketing digital", "description" => "Stratégies de marketing digital et réseaux sociaux.", "type" => Service::PEINTURE],
        ["service" => "Réparations de plomberie", "description" => "Réparations de plomberie et systèmes de chauffage.", "type" => Service::NETTOYAGE],
        ["service" => "Entretien de jardins", "description" => "Aménagement et entretien de jardins et espaces verts.", "type" => Service::PLOMBERIE],
        ["service" => "Photographie événementielle", "description" => "Services de photographie pour événements spéciaux.", "type" => Service::CHAUFFEUR],
        ["service" => "Transport et déménagement", "description" => "Transport et déménagement de biens et marchandises.", "type" => Service::BRICOLAGE],
        ["service" => "Installation de systèmes de sécurité", "description" => "Installation de systèmes de sécurité et surveillance.", "type" => Service::ELECTRICITE],
        ["service" => "Formation en compétences numériques", "description" => "Formation en compétences numériques pour entreprises.", "type" => Service::NETTOYAGE],
        ["service" => "Audits énergétiques", "description" => "Audits énergétiques et solutions d'économie d'énergie.", "type" => Service::CHAUFFEUR],
        ["service" => "Rénovation d'intérieur", "description" => "Rénovation et design d'intérieur pour maisons et bureaux.", "type" => Service::BRICOLAGE],
        ["service" => "Livraison de repas gourmets", "description" => "Livraison de repas gourmets à domicile.", "type" => Service::PLOMBERIE],
        ["service" => "Nettoyage après construction", "description" => "Service de nettoyage après construction ou rénovation.", "type" => Service::PEINTURE],
        ["service" => "Programmes de fitness", "description" => "Programmes de fitness en groupe ou individuel.", "type" => Service::ELECTRICITE],
        ["service" => "Planification de conférences", "description" => "Planification et gestion de conférences et séminaires.", "type" => Service::BRICOLAGE],
        ["service" => "Gestion de patrimoine", "description" => "Conseil en gestion de patrimoine et optimisation fiscale.", "type" => Service::PLOMBERIE],
        ["service" => "Création de boutiques en ligne", "description" => "Création de boutiques en ligne et e-commerce.", "type" => Service::CHAUFFEUR],
        ["service" => "Campagnes publicitaires en ligne", "description" => "Campagnes publicitaires en ligne et SEO.", "type" => Service::NETTOYAGE],
        ["service" => "Réparations d'électroménagers", "description" => "Services de réparation d'appareils électroménagers.", "type" => Service::PLOMBERIE],
        ["service" => "Entretien des pelouses", "description" => "Entretien des pelouses et arbres.", "type" => Service::PEINTURE],
        ["service" => "Photographie de produits", "description" => "Photographie de produits pour catalogues et sites web.", "type" => Service::CHAUFFEUR],
        ["service" => "Service de taxi", "description" => "Service de taxi et chauffeur privé.", "type" => Service::BRICOLAGE],
        ["service" => "Installation de serrures", "description" => "Consultation et installation de serrures et systèmes de sécurité.", "type" => Service::ELECTRICITE],
        ["service" => "Support technique à distance", "description" => "Support technique et assistance informatique à distance.", "type" => Service::PEINTURE],
        ["service" => "Consulting en recyclage", "description" => "Consulting en recyclage et gestion des déchets.", "type" => Service::NETTOYAGE],
        ["service" => "Construction de piscines", "description" => "Construction de piscines et spas.", "type" => Service::CHAUFFEUR],
        ["service" => "Cuisinier à domicile", "description" => "Cuisinier à domicile pour dîners privés.", "type" => Service::BRICOLAGE],
        ["service" => "Nettoyage industriel", "description" => "Nettoyage industriel et dégraissage.", "type" => Service::ELECTRICITE],
        ["service" => "Entraînement sportif", "description" => "Entraînement sportif pour athlètes.", "type" => Service::PLOMBERIE],
        ["service" => "Création de stands", "description" => "Création de stands et expositions pour salons professionnels.", "type" => Service::PEINTURE],
        ["service" => "Assistance en gestion financière", "description" => "Assistance à la gestion financière des PME.", "type" => Service::CHAUFFEUR],
        ["service" => "Design graphique", "description" => "Design graphique pour brochures et affiches.", "type" => Service::BRICOLAGE],
        ["service" => "Stratégie de marque", "description" => "Stratégie de marque et identité visuelle.", "type" => Service::PLOMBERIE],
        ["service" => "Réparation de véhicules", "description" => "Réparation et entretien de véhicules automobiles.", "type" => Service::ELECTRICITE],
        ["service" => "Installation de systèmes d'irrigation", "description" => "Pose et entretien de systèmes d'irrigation.", "type" => Service::NETTOYAGE],
        ["service" => "Reportage photo", "description" => "Reportage photo pour entreprises et particuliers.", "type" => Service::PEINTURE],
        ["service" => "Service de livraison express", "description" => "Service de livraison express.", "type" => Service::CHAUFFEUR],
        ["service" => "Installation d'alarmes", "description" => "Installation de systèmes d'alarme et caméras.", "type" => Service::BRICOLAGE],
        ["service" => "Développement de logiciels", "description" => "Développement de logiciels sur mesure.", "type" => Service::NETTOYAGE],
        ["service" => "Consulting en efficacité énergétique", "description" => "Consulting en efficacité énergétique.", "type" => Service::CHAUFFEUR],
        ["service" => "Revêtement de sol", "description" => "Revêtement de sol et travaux de carrelage.", "type" => Service::PLOMBERIE],
        ["service" => "Livraison de repas diététiques", "description" => "Préparation et livraison de repas diététiques.", "type" => Service::ELECTRICITE],
        ["service" => "Nettoyage de vitres", "description" => "Nettoyage de vitres et façades.", "type" => Service::BRICOLAGE],
        ["service" => "Coaching en gestion de stress", "description" => "Coaching en gestion de stress et bien-être.", "type" => Service::PEINTURE],
        ["service" => "Organisation de voyages d'affaires", "description" => "Organisation de voyages d'affaires et incentives.", "type" => Service::CHAUFFEUR],
        ["service" => "Audit en cybersécurité", "description" => "Audit et conseil en cybersécurité.", "type" => Service::ELECTRICITE],
        ["service" => "Animation de réseaux sociaux", "description" => "Animation de réseaux sociaux et community management.", "type" => Service::BRICOLAGE],
        ["service" => "Réparation de toitures", "description" => "Réparation de toitures et étanchéité.", "type" => Service::NETTOYAGE],
        ["service" => "Création de jardins d'intérieur", "description" => "Création de jardins d'intérieur et terrasses.", "type" => Service::PLOMBERIE],
        ["service" => "Photographie immobilière", "description" => "Photographie immobilière et de paysage.", "type" => Service::PEINTURE],
        ["service" => "Service de navette", "description" => "Services de navette aéroport et gare.", "type" => Service::CHAUFFEUR],
        ["service" => "Conseil en protection des données", "description" => "Conseil en protection des données personnelles.", "type" => Service::NETTOYAGE]
    ];

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 61; $i++) {
            $serv = new Service();
            $serv->setPrestataire($this->getReference('presta' . rand(1, 15) . '-user'));
            $serv->setTitre($this->services[$i - 1]['service']);
            $serv->setTarifs(rand(1, 100));
            $serv->setType($this->services[$i - 1]['type']);
            $serv->setDescription($this->services[$i - 1]['description']);
            $this->addReference('service' . $i, $serv);
            $manager->persist($serv);
        }
        for ($j = 0; $j < 180; $j++) {
            $that = $this->getReference('voyageur' . rand(1, 10) . '-user');
            $nott = new Note();
            $nott->setService($this->getReference('service' . rand(1, 60)));
            $nott->setUser($that);
            $nott->setNote(rand(1, 5));
            $manager->persist($nott);
        }
        for ($i = 1; $i < 150; $i++) {
            $n = rand(0, 31);
            $appartement = new Appartement();
            $appartement->setAddress($i . ' rue du bailleur');
            $appartement->setTitre('Appartement F2 au ' . $i . 'ème étage');
            $appartement->setPostalCode($this->locations[$n]['postal_code']);
            $appartement->setDescription('Appartement de type F2, situé au ' . $i . 'ème étage d\'un immeuble de 15 étages. Il est composé d\'un séjour, d\'une cuisine, d\'une chambre, d\'une salle de bain et d\'un WC. Il est équipé d\'un chauffage individuel électrique. L\'appartement est situé à proximité des commerces et des transports en commun. Il est disponible immédiatement.');
            $appartement->setShortDesc("Appartement de type F2, situé au " . $i . "ème étage d'un immeuble de 15 étages.");
            $appartement->setCity($this->locations[$n]['city']);
            $appartement->setCountry($this->locations[$n]['country']);
            $appartement->setSurface(50);
            $appartement->setNbVoyageurs(rand(5, 15));
            $appartement->setNbchambers(rand(1,3));
            $appartement->setBailleur($this->getReference('bailleurp' . rand(1, 6) . '-user'));
            $appartement->setNbBeds(rand(1, 5));
            $appartement->setNbBathrooms(rand(1, 3));
            $appartement->setPrice(rand(50, 200));
            $appartement->setCreatedAt(new \DateTime(sprintf('202%d-01-%02d', rand(2, 9), rand(1, 31))));
            $appartement->setUpdatedAt(new \DateTime(sprintf('202%d-01-%02d', rand(2, 9), rand(1, 31))));
            $this->addReference('appartement' . $i, $appartement);
            $manager->persist($appartement);
        }
        for ($j = 1; $j <= 6; $j++) {
            $plusie = new AppartPlus();
            $plusie->setIcon($j);
            for ($i = 1; $i <= rand(1, 149); $i++) {
                $plusie->addAppartement($this->getReference('appartement' . rand(1, 149)));
            }
            $manager->persist($plusie);
        }
        for ($i = 1; $i < 100; $i++) {
            $that = $this->getReference('voyageur' . rand(1, 10) . '-user');
            $loca = new Location();
            $loca->setLocataire($that);
            $num = rand(1, 29);
            $y = rand(0, 9);
            $z = rand(1,12);
            $date = new DateTime(sprintf('202%d-%02d-%02d', $y, $z, $num));
            $loca->setDateDebut($date);
            $date2 = new DateTime(sprintf('202%d-%02d-%02d', $y, $z, rand($num + 1, 31)));
            $loca->setDateFin($date2);
            $loca->setAppartement($this->getReference('appartement' . rand(1, 149)));
            $loca->setAdults(rand(1, 4));
            $loca->setKids(rand(0, 2));
            $loca->setBabies(rand(0, 1));
            for ($j = 1; $j <= rand(1, 10); $j++) {
                $loca->addService($this->getReference('service' . rand(1, 20)));
            }
            $price = 0;
            foreach ($loca->getServices() as $service) {
                $price += $service->getTarifs();
            }
            $days = $date->diff($date2)->days;
            $loca->setPrice($loca->getAppartement()->getPrice() * $days + $price);
            $this->addReference('location' . $i, $loca);
            for ($j = 1; $j <= 40; $j++) {
                $nott = new Note();
                $nott->setLocation($this->getReference('location' . $i));
                $nott->setUser($that);
                $nott->setNote(rand(1, 5));
                $manager->persist($nott);
            }
            $manager->persist($loca);
        }
        $manager->flush();
        foreach($this->comms as $comm){
            $comment = new Commentaire();
            $comment->setUser($this->getReference('voyageur' . rand(1, 10) . '-user'));
            $comment->setEntityId($this->getReference('appartement' . rand(1, 149))->getId());
            $comment->setType(Commentaire::APPART);
            $comment->setDate(new DateTime(sprintf('202%d-%02d-%02d', rand(0,9), rand(1,12),rand(1,31))));
            $comment->setCommentaire($comm);
            $manager->persist($comment);
        }
        $manager->flush();
        for ($i = 1; $i < 15; $i++) {
            $appartement = $this->getReference('appartement' . $i);
            $this->as->updateAppart($appartement->getId());
        }
    }
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
    public static function getGroups(): array
    {
        return ['prod', 'appartprod'];
    }
}
