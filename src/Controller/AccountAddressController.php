<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    private $entitymanager;

    /**
     * AccountAddressController constructor.
     * @param $entitymanager
     */
    public function __construct(EntityManagerInterface $entitymanager)
    {
        $this->entitymanager = $entitymanager;
    }


    /**
     * @Route("/account/adresses", name="app_account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

    /**
     * @Route("/account/ajouter-une-adresse", name="app_account_address_add")
     */
    public function add(Cart $cart, Request $request): Response
    {
        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());
            $this->entitymanager->persist($address);
            $this->entitymanager->flush();

            if ($cart->get()) {
                return $this->redirectToRoute('app_order');
            } else {
                return $this->redirectToRoute('app_account_address');
            }




        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/modifier-une-adresse/{id}", name="app_account_address_edit")
     */
    public function edit(Request $request, $id): Response
    {
        $address = $this->entitymanager->getRepository(Address::class)->findOneBy(['id' => $id]);

        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account_address');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->entitymanager->flush();

            return $this->redirectToRoute('app_account_address');


        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/supprimer-une-adresse/{id}", name="app_account_address_delete")
     */
    public function delete(Request $request, $id): Response
    {
        $address = $this->entitymanager->getRepository(Address::class)->findOneBy(['id' => $id]);

        if ($address && $address->getUser() == $this->getUser()) {
            $this->entitymanager->remove($address);
            $this->entitymanager->flush();
        }

        return $this->render('account/address.html.twig');
    }
}
