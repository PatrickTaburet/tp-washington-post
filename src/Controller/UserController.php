<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Model\SearchArticle;
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
        $searchArticle = new SearchArticle();
        $form = $this->createForm(SearchType::class, $searchArticle);
        $form->handleRequest($request);
        $data = $repo-> findAll();
        $articles = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page'  ,1),
            9
        );
        if ($form ->isSubmitted() && $form-> isValid()) {
            $searchArticle->page = $request->query->getInt( 'page', 1);
            $articles = $repo->findBySearch($searchArticle);

            return $this->render('article/searchResult.html.twig', [
                'articles'=> $articles,
                'form' => $form->createView()
            ]);
        }
        return $this->render('article/gallery.html.twig', [
            'articles'=> $articles,
            'form' => $form->createView(), 
        ]);
    }
  
}
