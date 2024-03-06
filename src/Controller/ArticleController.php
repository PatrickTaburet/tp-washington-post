<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/editor/article", name="article_")
 */
class ArticleController extends AbstractController
{
    
    /**
     * @Route("/", name="list")
     */
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo-> findAll();

        // // Preload user settings
        // foreach ($articles as $article) {
        //    $article->getUser()->getName() ; 
        //    $article->getUser()->getFirstname() ; 

        // }
        return $this->render('article/articles.html.twig', [
            'articles'=> $articles
        ]);
    }
    /**
     * @Route("/myArticles", name="myArticles")
     */
    public function myArticles(ArticleRepository $repo): Response
    {
        $articles = $repo-> findAll();

        // // Preload user settings
        // foreach ($articles as $article) {
        //    $article->getUser()->getName() ; 
        //    $article->getUser()->getFirstname() ; 

        // }
        return $this->render('article/myArticles.html.twig', [
            'articles'=> $articles
        ]);
    }

    
    /**
    * @Route("/create", name="create", methods= {"GET", "POST"})
    */
    public function Create(Request $request): Response
    {   
        // Vérifier si l'utilisateur est connecté et a le rôle ROLE_EDITOR
        if (!$this->getUser() || !$this->isGranted('ROLE_EDITOR')) {
            throw new AccessDeniedException('Vous devez être connecté et avoir le rôle ROLE_EDITOR pour créer un article.');
        }
        $article = new Article(); //Entity name

        $article->setUser($this->getUser());
        $form = $this->createForm(ArticleType::class, $article); // creation du form
        $form -> handleRequest($request);  // Gestion des données envoyées
        if ( $form->isSubmitted() && $form->isValid()){
            $sendDatabase = $this->getDoctrine()
                                 ->getManager();
            $sendDatabase->persist($article);
            $sendDatabase->flush();

            $this->addFlash('notice', 'Soumission réussie !!'); 

            return $this->redirectToRoute('article_list');
        }
        return $this->render('article/createForm.html.twig', [
            'controller_name' => 'MainController',
            'form' => $form->createView(),
        ]);
    }

       
    /**
    * @Route("/update/{id}", name="update", methods= {"GET", "POST"})
    */
    public function Update(Request $request, ArticleRepository $repo, $id): Response
    {
        $article = $repo-> find($id);
            
        $form = $this->createForm(ArticleType::class, $article); // creation du form
        $form -> handleRequest($request);  // Gestion des données envoyées
        if ( $form->isSubmitted() && $form->isValid()){
            $sendDatabase = $this->getDoctrine()
                                 ->getManager();
            $sendDatabase->persist($article);
            $sendDatabase->flush();

            $this->addFlash('notice', 'Modification réussie !!'); 

            return $this->redirectToRoute('article_list');
        }
        return $this->render('article/updateForm.html.twig', [
            'controller_name' => 'MainController',
            'form' => $form->createView()
        ]);
    }

     /**
    * @Route("/delete/{id}", name="delete", methods= {"GET", "POST"})
    */
    public function Delete(ArticleRepository $repo, $id): Response
    {
        $article = $repo-> find($id);
            
       
        $sendDatabase = $this->getDoctrine()
                                ->getManager();
        $sendDatabase->remove($article);
        $sendDatabase->flush();

        $this->addFlash('notice', 'Suppression réussie !!'); 

    
        return $this->redirectToRoute('article_list');
    }
    
}
