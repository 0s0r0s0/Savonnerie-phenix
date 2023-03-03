<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\Security\FirewallConfig\JsonLoginConfig;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="app_stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager, $reference): Response
    {

        /**
         * DEBUT MODULE TEST STRIPE
         */

        $order = $entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        if (!$order) {
            new JsonResponse(['error' => 'order']);
        }


        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        foreach ($order->getOrderDetails()->getValues() as $product) {
            $products_object = $entityManager->getRepository(Product::class)->findOneBy(['name' => $product->getProduct()]);
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN.'/illustrations-produits/'.$products_object->getIllustration()]
                    ],

                ],
                'quantity' =>$product->getQuantity(),
            ];
        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN]
                ],

            ],
            'quantity' => 1,
        ];


        Stripe::setApiKey('sk_test_51KzPX4IzTVniRx3AO3hhV7pjdguXZ0c9G6eErf4SQ7DYUsG3qNM90gB0bt5x3SlKYmDP1KQTWhL0y0EMFcCg18Jg00jnCTKZ2B');


        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                $products_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/validate/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);

        $entityManager->flush();

        return new JsonResponse(['id' => $checkout_session->id]);

        /**
         * FIN MODULE TEST STRIPE
         */
    }
}
