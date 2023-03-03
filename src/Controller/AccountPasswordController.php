<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    private $entityManager;

    /**
     * AccountPasswordController constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/compte/modifier-mon-mot-de-passe", name="app_account_password")
     */
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $old_pwd = $form->get('old_password')->getData();
            // Vérifie le mdp de l'user et le compare à la saisie de l'utilisateur
            if ($encoder->isPasswordValid($user, $old_pwd)) {
                $new_pwd = $form->get('new_password')->getData();
                $password = $encoder->hashPassword($user, $new_pwd);

                $user->setPassword($password);

                try {
                    $this->entityManager->flush();
                } catch (OptimisticLockException | ORMException $e) {
                }
                $notification = "Votre mot de passe a été mis à jour";
            }
            else {
                $notification = "Votre mot de passe actuel n'est pas le bon";
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
