<?php

namespace App\Controller;

use App\Entity\Feature;
use App\Entity\Header;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;

    /**
     * HomeController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $this->entityManager->getRepository(Product::class)->findBy(['isBest' => 1]),
            'headers' => $this->entityManager->getRepository(Header::class)->findBy(['isActive' => 1]),
            'features' => $this->entityManager->getRepository(Feature::class)->findBy(['isActive' => 1])
        ]);
    }
}
