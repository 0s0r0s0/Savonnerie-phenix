<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/newsletter")
 */
class NewsletterController extends AbstractController
{
    /**
     * @Route("/", name="app_newsletter_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $newsletters = $entityManager
            ->getRepository(Newsletter::class)
            ->findAll();

        return $this->render('newsletter/index.html.twig', [
            'newsletters' => $newsletters,
        ]);
    }

    /**
     * @Route("/new", name="app_newsletter_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($newsletter);
            $entityManager->flush();

            return $this->redirectToRoute('app_newsletter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('newsletter/new.html.twig', [
            'newsletter' => $newsletter,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_newsletter_show", methods={"GET"})
     */
    public function show(Newsletter $newsletter): Response
    {
        return $this->render('newsletter/show.html.twig', [
            'newsletter' => $newsletter,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_newsletter_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Newsletter $newsletter, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_newsletter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('newsletter/edit.html.twig', [
            'newsletter' => $newsletter,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_newsletter_delete", methods={"POST"})
     */
    public function delete(Request $request, Newsletter $newsletter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$newsletter->getId(), $request->request->get('_token'))) {
            $entityManager->remove($newsletter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_newsletter_index', [], Response::HTTP_SEE_OTHER);
    }
}
