<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
   /**
     * @Route("/gallery", name="gallery")
     */
    public function gallery(ArticleRepository $repo): Response
    {
        $articles = $repo-> findAll();

        // // Preload user settings
        // foreach ($articles as $article) {
        //    $article->getUser()->getName() ; 
        //    $article->getUser()->getFirstname() ; 

        // }
        return $this->render('article/gallery.html.twig', [
            'articles'=> $articles
        ]);
    }
}
