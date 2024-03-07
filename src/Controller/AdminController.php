<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/confirm/{id}", name="confirm")
     */
    public function confirmDelete(UserRepository $users, $id): Response
    {
        return $this->render('admin/confirm.html.twig', [
            'controller_name' => 'AdminController',
            'user' => $users->find($id)
        ]);
    }
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
    * @Route("/users", name="users")
    */
    public function usersList(UserRepository $users): Response
    {
        $allUsers = $users->findAll();

        // Preload Avatar objects
        foreach ($allUsers as $user) {
           $user->getAvatar();
        }

        return $this->render('admin/users.html.twig', [
            'users' => $allUsers
        ]);
    }
    /**
    * @Route("/users/edit/{id}", name="edit_user", methods= {"GET", "POST"})
    */
    public function editUser(Request $request, UserRepository $repo, $id) : Response
    {       
            $user = $repo->find($id);
            
            $userForm = $this->createForm(EditUserType::class, $user,[
                'is_admin' => false,
                'is_not_admin' => true,
                
            ]);
            $userForm -> handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {

                  // Handle the uploaded avatar image       
                $avatar = $user->getAvatar();
                $user->setAvatar($avatar);
                $entityManager = $this->getDoctrine()
                                      ->getManager();
                $entityManager -> persist($user);
                $entityManager -> flush();
                $user->removeFile(); // Delete the object file after persist to avoid errors
                $this ->addFlash('message', 'User edit succeed');

                return $this->redirectToRoute('admin_users');
            }

            return $this->render('admin/editUser.html.twig', [
                'user' => $user,
                'userForm' => $userForm->createView(),
                'isAdmin' => true,
            ]);
    } 

      /**
     * @Route("/users/delete/{id}", name="delete_user", methods= {"GET"})
     */
    public function deleteUser(UserRepository $repo, $id): Response
    {
        $user = $repo->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('message', 'User delete succeed');
        return $this->redirectToRoute('admin_users');
    }
}
