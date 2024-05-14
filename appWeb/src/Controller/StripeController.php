<?php

namespace App\Controller;

use App\Entity\Appartement;
use App\Entity\Location;
use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

class StripeController extends AbstractController
{
    const URL = "";
    #[Route('/stripe', name: 'app_stripe')]
    #[IsGranted("ROLE_USER")]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $request->get("confirm_location");
        $fform = $form['firstForm'];
        $appartId = $fform["appart"];
        $appart = $em->getRepository(Appartement::class)->find($appartId);
        $appartPrice = $form["price"];
        $adults = $fform["adults"];
        $kids = $fform["kids"];
        $babies = $fform["babies"];
        $services = $form['services'] ?? [];
        $dates = explode("-", trim($fform["date"]));
        $dates = array_map(function ($date) {
            return new \DateTime($date);
        }, $dates);
        $appartimgs = $appart->getImages();
        $appartimgs = array_map(function ($img) {
            return self::URL . 'images/appartements' . $img;
        }, $appartimgs);
        $loca = new \stdClass();
        $loca->appartement =$appart->getId();
        $loca->adults =$adults;
        $loca->kids =$kids;
        $loca->babies =$babies;
        $loca->dateDebut =$dates[0];
        $loca->dateFin =$dates[1];
        $loca->locataire =$user->getUserIdentifier();
        $loca->price =$appartPrice;
        $loca->service=$services;
        $uid = Uuid::v7();
        $request->getSession()->set('location', $loca);
        $request->getSession()->set('uid', $uid);

        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        $lineItems = [];
        $lineItems[] =
            [
                # Appartement
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $appartPrice * 100,
                    'product_data' => [
                        'name' => ($appart->getTitre()) ? $appart->getTitre() : "Appartement",
                        'images' => $appartimgs,
                        "description" => $appart->getShortDesc(),
                    ]
                ],
                'quantity' => 1,
            ];
        foreach ($services as $service) {
            $s = $em->getRepository(Service::class)->find($service);
            $lineItems[] =
                [
                    # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $s->getTarifs() * 100,
                        'product_data' => [
                            'name' => $s->getTitre(),
                            'images' => ['http://placehold.it/200/200'], // $service->getImages()->getPath();
                            "description" => $s->getDescription(),
                        ]
                    ],
                    'quantity' => 1,
                ];
        }

        // dd($request->get("confirm_location"), $dates, $appart, $lineItems);
        $checkout_session = \Stripe\Checkout\Session::create([
            'customer_email' => $user->getUserIdentifier(),
            'submit_type' => 'book',
            "consent_collection" => ["terms_of_service" => "required"],
            'payment_method_types' => ['card'],
            'automatic_tax' => ['enabled' => true],
            'billing_address_collection' => 'auto',
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('index', ["c" => $uid], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('appart_detail', ['id' => $appartId], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        return $this->redirect($checkout_session->url, 302);
    }
}
