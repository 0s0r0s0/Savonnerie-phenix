<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    /**
     * OrderValidateController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/commande/validate/{stripeSessionId}", name="app_order_validate")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if ($order->getState() == 4) {

            // Vidage session Cart (panier)
            $cart->remove();

            $order->setState(0);
            $this->entityManager->flush();

            $mail = new Mail();

            // MODIFIER LE MAIL DE VALIDATION DE COMMANDE !!

            $content = "Bonjour". $order->getUser()->getFirstName() ."<br/> "  ;

            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), "Votre commande la savonnerie de Bibi est validée ...", $content);
        }

        return $this->render('order_validate/index.html.twig', [
            'order' => $order
        ]);
    }
}
