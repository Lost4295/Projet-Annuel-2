<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        $user = $this->getUser();
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        $checkout_session = \Stripe\Checkout\Session::create([
            'customer_email' => "blabla@ofekf.fr",
            'submit_type' => 'book',
            "consent_collection" => ["terms_of_service" => "required"],
            'payment_method_types' => ['card'],
            'automatic_tax' => ['enabled' => true],
            'billing_address_collection' => 'auto',
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => 2000,
                    'product_data' => [
                        'name' => 'Placeholder Product Name',
                        'images' => ['http://placehold.it/200/200'],
                    ]
                ],
                'quantity' => 1,
            ], [
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => 2000,
                    'product_data' => [
                        'name' => 'Placeholder Product Name',
                        'images' => ['http://placehold.it/200/200'],
                    ]
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('index', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('index', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        return $this->redirect($checkout_session->url, 302);
    }
}
