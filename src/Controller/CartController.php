<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RequestContext;

class CartController extends AbstractController
{
    private $entityManager;
    private $request;


    /**
     * CartController constructor.
     * @param EntityManagerInterface $entityManager
     * @param RequestContext $request
     */
    public function __construct(EntityManagerInterface $entityManager, RequestContext $request)
    {
        $this->entityManager = $entityManager;
        $this->request = $request;
    }

    /**
     * @Route("/mon-panier", name="app_cart")
     */
    public function index(Cart $cart): Response
    {

        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getFull(),
        ]);
    }

    /**
     * @Route("/cart/add/{id}/{origin}", name="app_add_to_cart")
     */
    public function add(Cart $cart, $id, $origin)
    {
        $cart->add($id);

        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

        $this->addFlash('notice', $product->getName() .' a bien été ajouté à votre panier');

        return $this->redirectToRoute($origin);

    }

    /**
     * @Route("/cart/remove", name="app_remove_my_cart")
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();


    }

    /**
     * @Route("/cart/delete/{id}", name="app_delete_to_cart")
     */
    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);

        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/decrease/{id}", name="app_decrease_to_cart")
     */
    public function decrease(Cart $cart, $id): Response
    {
        $cart->decrease($id);

        return $this->redirectToRoute('app_cart');
    }
}
