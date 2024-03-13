<?php

namespace App\Repository;

use App\Entity\Article;
use App\Model\SearchArticle;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
       /**
     * @var PaginatorInterface
     */
    private $paginatorInterface;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Article::class);
        $this->paginatorInterface = $paginatorInterface;

    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    /**
     * Get articles thanks to SearchArticle value
     * 
     * @param SearchArticle $searchArticle
     * @return PaginationInterface
     * 
     */

   public function findBySearch(SearchArticle $searchArticle): PaginationInterface
   {
    
        $data = $this->createQueryBuilder('p')
            -> addOrderBy('p.updatedAt', 'DESC');

        if(!empty($searchArticle->q)){
            $data = $data
                -> andWhere("p.title LIKE :q")
                -> setParameter('q',"%{$searchArticle->q}%");
        }
        $data = $data
            ->getQuery()
            ->getResult();
        $articles = $this-> paginatorInterface->paginate($data, $searchArticle->page, 9);

        return $articles;
   }
}
