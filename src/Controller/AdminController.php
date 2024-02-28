<?php

namespace App\Controller;

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
        return $this->render('admin/users.html.twig', [
            'users' => $users->findAll()
        ]);
    }
    /**
    * @Route("/users/edit/{id}", name="edit_user", methods= {"GET", "POST"})
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
                return $this->redirectToRoute('admin_users');
            }

            return $this->render('admin/edituser.html.twig', [
                'userForm' => $form->createView()
            ]);
    } 

    //   /**
    //  * @Route("/users/delete/{id}", name="delete_user", methods= {"GET"})
    //  */
    // public function deleteUser(UserRepository $repo, $id): Response
    // {
    //     $user = $repo->find($id);
    //     $entityManager = $this->getDoctrine()->getManager();
    //     $entityManager->remove($user);
    //     $entityManager->flush();

    //     $this->addFlash('message', 'User delete succeed');
    //     return $this->redirectToRoute('admin_users');
    // }
}
