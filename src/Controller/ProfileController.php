<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{   

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
           
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
    * @Route("/profile/{id}", name="profile", methods= {"GET", "POST"})
    */
    public function editUser(Request $request, UserRepository $repo, $id) : Response
    {       
            $user = $repo->find($id);
            $form = $this->createForm(EditUserType::class, $user);
            $form -> handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()
                                      ->getManager();
                $entityManager -> persist($user);
                $entityManager -> flush();

                $this ->addFlash('message', 'User edit succeed');
                return $this->redirectToRoute('home');
            }

            return $this->render('profile/profileModal.html.twig', [
                'userForm' => $form->createView()
            ]);
    } 
}
