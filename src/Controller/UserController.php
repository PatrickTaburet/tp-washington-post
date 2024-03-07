<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
   /**
     * @Route("/gallery", name="gallery")
     */
    public function gallery(ArticleRepository $repo, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $data = $repo-> findAll();
        $articles = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page'  ,1),
            9
        );
        return $this->render('article/gallery.html.twig', [
            'articles'=> $articles
        ]);
    }
  
}
