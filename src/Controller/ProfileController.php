<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\User;
use App\Form\AvatarType;
use App\Form\EditUserType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\AvatarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{   

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UploaderHelper $uploaderHelper): Response
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
        }

        $avatar = new Avatar();
        $user->setAvatar($avatar);
        $avatarForm = $this->createForm(AvatarType::class, $avatar);
        $avatarForm->handleRequest($request);
        if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
        
            // handle avatar upload
            if ($avatar->getImageFile()) {
                $entityManager->persist($avatar);
                $entityManager->flush();
            }
        
            return $this->redirectToRoute('app_login');
        }        

        return $this->render('profile/register.html.twig', [
            'registrationForm' => $form->createView(),
            'avatarForm' => $avatarForm->createView(),
        ]);
    }

    /**
    * @Route("/profile/{id}", name="profile", methods= {"GET", "POST"})
    */
    public function editUser(Request $request, UserRepository $repo, $id) : Response
    {       
            $user = $repo->find($id);
            $avatar = $user->getAvatar();
            $userForm = $this->createForm(EditUserType::class, $user);
            $avatarForm = $this->createForm(AvatarType::class, $avatar);
            $userForm -> handleRequest($request);
            $avatarForm -> handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {
                $entityManager = $this->getDoctrine()
                                      ->getManager();
                $entityManager -> persist($user);
                $entityManager -> flush();

                $this ->addFlash('message', 'User edit succeed');
                return $this->redirectToRoute('home');
            }

            if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
                $entityManager = $this->getDoctrine()
                                      ->getManager();
                $entityManager -> persist($avatar);
                $entityManager -> flush();

                $this ->addFlash('message', 'Profile picture edit succeed');
                return $this->redirectToRoute('home');
            }

            return $this->render('profile/profileModal.html.twig', [
                'user' => $user,
                'userForm' => $userForm->createView(),
                'avatarForm' => $avatarForm->createView(),
            ]);
    } 
}
