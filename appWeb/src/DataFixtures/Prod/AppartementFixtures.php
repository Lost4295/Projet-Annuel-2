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
        ["service" => "Éclat Propreté", "description" => "Service de nettoyage complet pour bureaux, maisons et espaces commerciaux, utilisant des produits écologiques pour un environnement sain.", "type" => Service::NETTOYAGE],
        ["service" => "Électro Solution", "description" => "Installation et maintenance de systèmes électriques résidentiels et commerciaux, garantissant sécurité et conformité aux normes.", "type" => Service::ELECTRICITE],
        ["service" => "Peinture Parfaite", "description" => "Service de peinture intérieure et extérieure, offrant des finitions impeccables et durables pour transformer vos espaces.", "type" => Service::PEINTURE],
        ["service" => "Chauffeur de Confiance", "description" => "Service de chauffeur privé pour déplacements professionnels et personnels, assurant confort et ponctualité.", "type" => Service::CHAUFFEUR],
        ["service" => "Bricolage Expert", "description" => "Petits travaux de réparation et d'amélioration de l'habitat, de l'assemblage de meubles à l'installation de cadres et étagères.", "type" => Service::BRICOLAGE],
        ["service" => "Plomberie Premium", "description" => "Réparation et installation de systèmes de plomberie pour cuisines, salles de bain et extérieurs, avec une finition professionnelle.", "type" => Service::PLOMBERIE],
        ["service" => "Nettoyage Éclatant", "description" => "Nettoyage spécialisé pour tapis, moquettes et meubles rembourrés, assurant une propreté durable et une fraîcheur inégalée.", "type" => Service::NETTOYAGE],
        ["service" => "Bricolage Rapide", "description" => "Assistance pour vos projets de bricolage, avec des solutions rapides et efficaces pour un résultat professionnel.", "type" => Service::BRICOLAGE],
        ["service" => "Courant Express", "description" => "Dépannage électrique rapide et efficace, disponible 24/7 pour résoudre tous vos problèmes d'électricité en un clin d'œil.", "type" => Service::ELECTRICITE],
        ["service" => "Aqua Pro Services", "description" => "Entretien et rénovation de vos installations de plomberie, assurant une fonctionnalité optimale et sans fuites.", "type" => Service::PLOMBERIE],
        ["service" => "Couleurs et Style", "description" => "Peinture décorative et personnalisée pour ajouter une touche unique et élégante à vos murs et plafonds.", "type" => Service::PEINTURE],
        ["service" => "Brillance Plus", "description" => "Nettoyage en profondeur de vos espaces résidentiels et professionnels, garantissant des résultats impeccables et une hygiène irréprochable.", "type" => Service::NETTOYAGE],
        ["service" => "Plombier Express", "description" => "Intervention rapide pour tous vos problèmes de plomberie, disponible 24/7 pour une tranquillité d'esprit garantie.", "type" => Service::PLOMBERIE],
        ["service" => "Voyage VIP", "description" => "Transport haut de gamme avec chauffeur, offrant une expérience luxueuse et sécurisée pour tous vos trajets.", "type" => Service::CHAUFFEUR],
        ["service" => "BricoSolution", "description" => "Service complet de bricolage pour votre maison, incluant réparations, montages et petites rénovations avec une attention aux détails.", "type" => Service::BRICOLAGE],
        ["service" => "Énergie Pro", "description" => "Solutions électriques sur mesure pour vos projets de construction et de rénovation, assurant performance et fiabilité.", "type" => Service::ELECTRICITE],
        ["service" => "ProNet Solutions", "description" => "Service de nettoyage sur mesure pour maisons, appartements et locaux commerciaux, avec des équipements modernes et des produits respectueux de l'environnement.", "type" => Service::NETTOYAGE],
        ["service" => "Prestige Drive", "description" => "Service de chauffeur privé pour événements spéciaux et transferts aéroport, garantissant une discrétion et un service impeccable.", "type" => Service::CHAUFFEUR],
        ["service" => "Brico Zen", "description" => "Amélioration et entretien de votre maison sans stress, avec des services de bricolage fiables et rapides.", "type" => Service::BRICOLAGE],
        ["service" => "Plomberie Éco", "description" => "Solutions de plomberie écologiques et durables, utilisant des matériaux respectueux de l'environnement pour une maison plus verte.", "type" => Service::PLOMBERIE],
        ["service" => "Peinture Éclat", "description" => "Rénovation de vos espaces avec des peintures de haute qualité, garantissant des résultats lumineux et durables.", "type" => Service::PEINTURE],
        ["service" => "Voltage Expert", "description" => "Service de diagnostic et réparation pour tous vos équipements électriques, avec des techniciens qualifiés et expérimentés.", "type" => Service::ELECTRICITE],
        ["service" => "BricoPro", "description" => "Travaux de bricolage pour la maison et le jardin, réalisés par des professionnels qualifiés pour un résultat impeccable.", "type" => Service::BRICOLAGE],
        ["service" => "Hydro Solution", "description" => "Service complet d'installation et de réparation de plomberie, offrant des solutions efficaces et économiques pour tous vos besoins.", "type" => Service::PLOMBERIE],
        ["service" => "Elite Chauffeur", "description" => "Chauffeurs professionnels disponibles 24/7 pour tous vos déplacements, avec des véhicules de luxe pour une expérience de premier ordre.", "type" => Service::CHAUFFEUR],
        ["service" => "ÉcoNet Service", "description" => "Nettoyage écologique pour un environnement propre et sain, utilisant des techniques vertes pour un impact minimal sur la planète.", "type" => Service::NETTOYAGE],
        ["service" => "Plomberie Parfaite", "description" => "Services de plomberie résidentielle et commerciale avec une attention particulière aux détails et à la satisfaction du client.", "type" => Service::PLOMBERIE],
        ["service" => "Palette Pro", "description" => "Spécialistes en peinture résidentielle et commerciale, avec des solutions sur mesure pour répondre à tous vos besoins esthétiques.", "type" => Service::PEINTURE],
        ["service" => "Conduite Royale", "description" => "Transport privé avec chauffeur, spécialisé dans les trajets VIP et les services personnalisés pour répondre à toutes vos exigences.", "type" => Service::CHAUFFEUR],
        ["service" => "À Votre Service Brico", "description" => "Services de bricolage sur mesure, adaptés à vos besoins spécifiques, de la réparation à la création de solutions pratiques pour votre maison.", "type" => Service::BRICOLAGE],
        ["service" => "Électro Zen", "description" => "Installation de systèmes électriques intelligents et écoénergétiques pour améliorer le confort et réduire les coûts énergétiques.", "type" => Service::ELECTRICITE],
        ["service" => "Nuance Design", "description" => "Service de peinture d'intérieur professionnel, créant des ambiances harmonieuses et personnalisées pour chaque pièce de votre maison.", "type" => Service::PEINTURE],
        ["service" => "Clean & Shine", "description" => "Nettoyage détaillé pour tous types de surfaces, de la cuisine à la salle de bain, en passant par les bureaux et les espaces commerciaux.", "type" => Service::NETTOYAGE],
        ["service" => "Chauffeur Express", "description" => "Service de chauffeur rapide et fiable pour vos déplacements urgents en ville ou en banlieue, avec un souci constant de votre confort.", "type" => Service::CHAUFFEUR],
        ["service" => "BricoMaster", "description" => "Assistance pour tous vos projets de bricolage, garantissant des finitions soignées et durables pour un habitat toujours au top.", "type" => Service::BRICOLAGE],
        ["service" => "Power Perfect", "description" => "Services complets d'installation et de réparation électrique, offrant des solutions sûres et durables pour vos besoins domestiques et professionnels.", "type" => Service::ELECTRICITE],
        ["service" => "AquaTech Pro", "description" => "Expertise en réparation de fuites, débouchage de canalisations et installation de systèmes de plomberie modernes et efficaces.", "type" => Service::PLOMBERIE],
        ["service" => "Finition Exquise", "description" => "Peinture haut de gamme pour des finitions parfaites, apportant une touche de luxe à vos intérieurs et extérieurs.", "type" => Service::PEINTURE],
        ["service" => "First Class Ride", "description" => "Chauffeurs expérimentés pour des trajets en toute sécurité et sérénité, avec des véhicules équipés pour votre confort optimal.", "type" => Service::CHAUFFEUR],
        ["service" => "BricoDépannage", "description" => "Services de dépannage et de réparation pour toutes les petites urgences de bricolage à la maison.", "type" => Service::BRICOLAGE],
        ["service" => "Plombier Zenith", "description" => "Service de plomberie haut de gamme pour installations neuves et rénovations, garantissant des travaux soignés et durables.", "type" => Service::PLOMBERIE],
        ["service" => "Électro-Maestro", "description" => "Équipe d'experts en électricité pour tous types de travaux, de l'installation de circuits à la mise en conformité de vos installations.", "type" => Service::ELECTRICITE],
        ["service" => "Pureté Pro", "description" => "Service de nettoyage professionnel et rapide pour maisons, bureaux et commerces, avec une attention particulière aux détails.", "type" => Service::NETTOYAGE],
        ["service" => "Reflets et Couleurs", "description" => "Peinture décorative et murale, spécialisée dans la création d’effets spéciaux et de motifs uniques pour sublimer vos espaces.", "type" => Service::PEINTURE],
        ["service" => "Luxe Mobilité", "description" => "Service de transport de luxe avec chauffeur pour voyages d'affaires et loisirs, garantissant une expérience exclusive et sans tracas.", "type" => Service::CHAUFFEUR],
        ["service" => "BricoFacile", "description" => "Solutions de bricolage faciles et rapides pour l'entretien et l'amélioration de votre maison, assurées par des experts.", "type" => Service::BRICOLAGE],
        ["service" => "Nettoyage Zenith", "description" => "Entretien régulier et nettoyage ponctuel pour maintenir vos espaces en parfait état, avec des solutions adaptées à vos besoins.", "type" => Service::NETTOYAGE],
        ["service" => "Chauffeur Prestige", "description" => "Service de chauffeur professionnel pour événements corporatifs et personnels, avec une attention particulière aux détails et à la satisfaction du client.", "type" => Service::CHAUFFEUR],
        ["service" => "Eau Confort", "description" => "RSolutions de plomberie pour améliorer le confort de votre habitat, de la réparation de chauffe-eau à l'installation de systèmes de filtration.", "type" => Service::PLOMBERIE],
        ["service" => "Courant Continu", "description" => "Maintenance préventive et corrective de vos installations électriques pour garantir une performance optimale en toute sécurité.", "type" => Service::ELECTRICITE],
        ["service" => "BricoSérénité", "description" => "Prise en charge complète de vos besoins de bricolage, avec des services personnalisés pour un intérieur toujours bien entretenu.", "type" => Service::BRICOLAGE],
        ["service" => "Peinture Prestige", "description" => "Service de peinture professionnelle avec une attention particulière aux détails, utilisant des matériaux de qualité supérieure pour des résultats durables.", "type" => Service::PEINTURE],
        ["service" => "Voyageur Élite", "description" => "Transport sur mesure avec chauffeur pour toutes vos occasions, offrant ponctualité, sécurité et un service de haute qualité.", "type" => Service::CHAUFFEUR],
        ["service" => "Volt & Co", "description" => "Spécialistes en installation de réseaux électriques pour bureaux et habitations, avec une attention particulière à la qualité et à la sécurité.", "type" => Service::ELECTRICITE],
        ["service" => "Sparkle Service", "description" => "Nettoyage résidentiel et commercial de haute qualité, offrant des résultats éclatants et une satisfaction garantie.", "type" => Service::NETTOYAGE],
        ["service" => "Électricité Avantage", "description" => "Solutions électriques innovantes pour vos projets résidentiels et commerciaux, avec un service rapide et des tarifs compétitifs.", "type" => Service::ELECTRICITE],
        ["service" => "Plomberie Expert", "description" => "Équipe de plombiers qualifiés pour résoudre tous vos problèmes de plomberie, avec un service rapide et des tarifs transparents.", "type" => Service::PLOMBERIE],
        ["service" => "Teintes et Ambiances", "description" => "Création d'ambiances sur mesure avec une large gamme de couleurs et de finitions, adaptée à vos goûts et à votre style de vie.", "type" => Service::PEINTURE],
        ["service" => "Maestro des Couleurs", "description" => "Service de peinture artistique et décorative, transformant vos murs en véritables œuvres d'art grâce à des techniques innovantes.", "type" => Service::PEINTURE],
        ["service" => "Clarté Express", "description" => "Service de nettoyage rapide et efficace pour maisons et bureaux, utilisant des produits de qualité pour un résultat impeccable.", "type" => Service::NETTOYAGE]
    ];

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= count($this->services); $i++) {
            $serv = new Service();
            $serv->setPrestataire($this->getReference('presta' . rand(1, 15) . '-user'));
            $serv->setTitre($this->services[$i - 1]['service']);
            $serv->setTarifs(rand(1, 100));
            for ($la = 0; $la < rand(1, 9); $la++) {
                $nom =Service::TYPE_LIST[$this->services[$i - 1]['type']];
                $var = rand(1, 9);
                while (!file_exists(__DIR__ . '/images/'. $nom . $var . '.jpg')) {
                    $var = rand(1, 9);
                }
                
                $serv->addImage($nom . $var . '.jpg');
                copy(__DIR__ . '/images/' .$nom . $var . '.jpg', __DIR__ . '/../../../public/images/services/' .$nom. $var . '.jpg');
            }
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
            if (rand(1, 2) == 1) {
                $var = rand(1, 500);
                while (!file_exists(__DIR__ . '/images/apartment_' . $var . '.jpg')) {
                    $var = rand(1, 500);
                }
                $appartement->addImage('apartment_' . $var . '.jpg');
                copy(__DIR__ . '/images/apartment_' . $var . '.jpg', __DIR__ . '/../../../public/images/appartements/apartment_' . $var . '.jpg');
            } else {
                $var = rand(1, 500);
                while (!file_exists(__DIR__ . '/images/house_' . $var . '.jpg')) {
                    $var = rand(1, 500);
                }
                $appartement->addImage('house_' . $var . '.jpg');
                copy(__DIR__ . '/images/house_' . $var . '.jpg', __DIR__ . '/../../../public/images/appartements/house_' . $var . '.jpg');
                while (!file_exists(__DIR__ . '/images/garage_' . $var . '.jpg')) {
                    $var = rand(1, 500);
                }
                $appartement->addImage('garage_' . $var . '.jpg');
                copy(__DIR__ . '/images/garage_' . $var . '.jpg', __DIR__ . '/../../../public/images/appartements/garage_' . $var . '.jpg');
            }
            $nbbath = rand(1, 3);
            for ($j = 1; $j <= $nbbath; $j++) {
                $var = rand(1, 500);
                while (!file_exists(__DIR__ . '/images/bath_' . $var . '.jpg')) {
                    $var = rand(1, 500);
                }
                $appartement->addImage('bath_' . $var . '.jpg');
                copy(__DIR__ . '/images/bath_' . $var . '.jpg', __DIR__ . '/../../../public/images/appartements/bath_' . $var . '.jpg');
            }
            $nbbed = rand(1, 5);
            for ($j = 1; $j <= $nbbed; $j++) {
                $var = rand(1, 500);
                while (!file_exists(__DIR__ . '/images/bed_' . $var . '.jpg')) {
                    $var = rand(1, 500);
                }
                $appartement->addImage('bed_' . $var . '.jpg');
                copy(__DIR__ . '/images/bed_' . $var . '.jpg', __DIR__ . '/../../../public/images/appartements/bed_' . $var . '.jpg');
            }
            for ($j = 1; $j <= rand(1, 3); $j++) {
                $var = rand(1, 500);
                while (!file_exists(__DIR__ . '/images/din_' . $var . '.jpg')) {
                    $var = rand(1, 500);
                }
                $appartement->addImage('din_' . $var . '.jpg');
                copy(__DIR__ . '/images/din_' . $var . '.jpg', __DIR__ . '/../../../public/images/appartements/din_' . $var . '.jpg');
            }
            for ($j = 1; $j <= rand(1, 2); $j++) {
                $var = rand(1, 500);
                while (!file_exists(__DIR__ . '/images/kitchen_' . $var . '.jpg')) {
                    $var = rand(1, 500);
                }
                $appartement->addImage('kitchen_' . $var . '.jpg');
                copy(__DIR__ . '/images/kitchen_' . $var . '.jpg', __DIR__ . '/../../../public/images/appartements/kitchen_' . $var . '.jpg');
            }
            for ($j = 1; $j <= rand(1, 2); $j++) {
                $var = rand(1, 500);
                while (!file_exists(__DIR__ . '/images/living_' . $var . '.jpg')) {
                    $var = rand(1, 500);
                }
                $appartement->addImage('living_' . $var . '.jpg');
                copy(__DIR__ . '/images/living_' . $var . '.jpg', __DIR__ . '/../../../public/images/appartements/living_' . $var . '.jpg');
            }
            $appartement->setNbVoyageurs(rand(5, 15));
            $appartement->setNbchambers(rand(1, 3));
            $appartement->setBailleur($this->getReference('bailleurp' . rand(1, 6) . '-user'));
            $appartement->setNbBeds($nbbed);
            $appartement->setNbBathrooms($nbbath);
            $appartement->setPrice(rand(15, 200));
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
            $z = rand(1, 12);
            $date = new DateTime(sprintf('202%d-%02d-%02d', $y, $z, $num));
            $loca->setDateDebut($date);
            $date2 = new DateTime(sprintf('202%d-%02d-%02d', $y, $z, rand($num + 1, 31)));
            $loca->setDateFin($date2);
            $loca->setDateha(new DateTime(sprintf('202%d-%02d-%02d', $y, $z, rand($num, 31))));
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
            $manager->flush();
        }
        $manager->flush();
        foreach ($this->comms as $comm) {
            $comment = new Commentaire();
            $comment->setUser($this->getReference('voyageur' . rand(1, 10) . '-user'));
            $comment->setEntityId($this->getReference('appartement' . rand(1, 149))->getId());
            $comment->setType(Commentaire::APPART);
            $comment->setDate(new DateTime(sprintf('202%d-%02d-%02d', rand(0, 9), rand(1, 12), rand(1, 31))));
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
