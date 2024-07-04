<?php

namespace App\Controller;

use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Stripe\StripeClient;

class StripeController extends AbstractController
{

    public function calculateOrderAmount(array $items): int
    {
        // Replace this constant with a calculation of the order's amount
        // Calculate the order total on the server to prevent
        // people from directly manipulating the amount on the client
        return 1400;
    }


    #[Route('/create-payment-intent', name: 'create_payment_intent', methods: ['GET', 'POST'])]
    public function createPaymentIntent(): Response
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            exit();
        }
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid Content Type']);
            exit();
        }
        if (!isset($_POST["Auth"]) || $_POST["Auth"]!= "paris_caretaker_services") {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized.'. $_POST["Auth"]]);
            exit();
        }
        $stripe = new StripeClient($_ENV["STRIPE_SECRET"]);


        header('Content-Type: application/json');

        try {
            $jsonStr = file_get_contents('php://input');
            $jsonObj = json_decode($jsonStr);

            $customer = $stripe->customers->create([
                'email' => $jsonObj->user->email,
                'name' => $jsonObj->user->name,
                'address'=> [
                    'city' => $jsonObj->user->city,
                    'country' => $jsonObj->user->country,
                    'line1' => $jsonObj->user->address,
                    'postal_code' => $jsonObj->user->postal_code,
                ]
            ]);
            // Create a PaymentIntent with amount and currency
            $ephemeralKey = $stripe->ephemeralKeys->create(
                ['customer' => $customer->id],
                ['stripe_version' => '2022-08-01']);
            // retrieve JSON from POST body


            // Create a PaymentIntent with amount and currency
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $jsonObj->appart->price * 100,
                'description'=> $jsonObj->appart->shortDesc,
                'currency' => 'eur',
                'customer' => $customer->id,
                // In the latest version of the API, specifying the `automatic_payment_methods` parameter is optional because Stripe enables its functionality by default.
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            $output = [
                'paymentIntent' => $paymentIntent->client_secret,
                'ephemeralKey' => $ephemeralKey->secret,
                'customer' => $customer->id,
                'publishableKey' => $_ENV["STRIPE_KEY"],

            ];

            return $this->json($output);
        } catch (Error $e) {
            http_response_code(500);
            return $this->json(['error' => $e->getMessage()]);
        }
    }
}
