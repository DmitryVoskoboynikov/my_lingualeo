<?php

// src/Controller/UserController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormError;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function notifications(): Response
    {
        // get the user information and notifications somehow
        $userFirstName = '...';
        $userNotifications = ['...', '...'];

        // the template path is the relative file path from `templates/`
        return $this->render('user/notifications.html.twig', [
            // this array defines the variables passed to the template,
            // where the key is the variable name and the value is the variable value
            // (Twig recommends using snake_case variable names: 'foo_bar' instead of 'fooBar')
            'user_first_name' => $userFirstName,
            'notifications' => $userNotifications,
        ]);
    }

    public function auth(Request $request, UserRepository $rep): Response
    {
        $form = $this->createFormBuilder(null, [
                'action' => '/auth',
                'method' => 'POST'
            ]
        )
            ->add('email', TextType::class)
            ->add('password', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = $rep->findOneBy(['email' => $data['email']]);
            if ($user && ($user->getPassword() == $data['password'])) {
                return $this->redirectToRoute('user_notification');
            } else {
                $form->addError(
                    new FormError('Пользователя с такой парой логин пароль не существует.')
                );
            }

        }

        return $this->render('user/auth.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function reg(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $user->setRoles(['ROLE_USER']);

            // Save
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_notification');
        }

        return $this->render('user/reg.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}