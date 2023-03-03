<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Newsletter;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/inscription", name="app_register")
     */
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            if (isset($_POST['register']['newsletter'])) {
                $newsletter = new Newsletter();
                $newsletter->setEmail($user->getEmail());
                $this->entityManager->persist($newsletter);
                $this->entityManager->flush();
            }

            $search_email = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            if (!$search_email) {
                $password = $encoder->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $mail = new Mail();

                // MODIFIER LE MAIL DE BIENVENUE !!

                $content = "Bonjour". $user->getFirstName() ."<br/> sur le site de la savonnerie de Bibi"  ;

                $mail->send($user->getEmail(), $user->getFirstName(), "Bienvenue sur le site ...", $content);

                $notification = "Votre inscription a réussie ! Vous pouvez vous connecter à votre compte";
            } else {
                $notification = "L'email renseignée existe déjà !";
            }



        }

        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
