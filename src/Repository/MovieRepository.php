<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 *
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function add(Movie $entity, bool $flush = false): void
    {
       // pour faire un select on doit utiliser l'entity manager

        $entityManager = $this->getEntityManager();
        
       // on demande à l'entityManager de persist
        $entityManager->persist($entity);

    
        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Movie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * recherche d'un film par son id avec DQL
     *
     * @param integer $id
     * @return Movie|null
     */
    public function findDQL(int $id): ?Movie
    {
       
        // on utilise les propriétés de l'objet dans la requête
        $query = $this->getEntityManager()->createQuery('SELECT m FROM App\Entity\Movie m WHERE m.id = ' . $id);
        // getResult() nous renvoit un tableau d'objet ou un tableau vide
        $movies = $query->getResult();

        $query = $this->getEntityManager()->createQuery('SELECT m, c, p, g
            FROM App\Entity\Movie m 
            JOIN m.castings c 
            JOIN c.person p
            JOIN m.genres g
            WHERE m.id  = ' . $id .'
            ORDER BY c.creditOrder'
        );
            
        $movies = $query->getResult();

        // si on veut un seul résultat, je renvoie le premier résultat
        if (count($movies) === 1) {
            return $movies[0];
        }
        
        // sinon on ne renvoie rien
        return null;
    }

    /**
     * recherche d'un film par son id avec QueryBuilder
     *
     * @param integer $id
     * @return Movie|null
     */
    public function findQB(int $id): ?Movie
    {
       
        // je choisit l'alias de mon objet
        $movies = $this->createQueryBuilder('m')
            // je fait un where avec un paramètre
            ->andWhere('m.id = :movie_id')
            // je donne une valeur à mon paramètre
            ->setParameter('movie_id', $id)
            // je demande l'objet query
            ->getQuery()
            // je récupère les résultats
            ->getResult();

       
        $movies = $this->createQueryBuilder('m')
            ->andWhere('m.id = :movie_id')
            ->setParameter('movie_id', $id)
            
            // je rajoute une jointure sur castings
            ->innerJoin('m.castings', 'c')
     
            ->addSelect('c')
        
            ->innerJoin('c.person', 'p')
        
            ->addSelect('p')

            ->innerJoin('m.genres', 'g')
            ->addSelect('g')

            ->getQuery()
            ->getResult();

        if (count($movies) === 1) {
            return $movies[0];
        }
        
    }

    //  trier par durée 
    public function findAllOrderByDuration(): array
    {
       
        return $this->createQueryBuilder('m')
        
            ->orderBy("m.duration", "ASC")
          
            ->setMaxResults(10)
        
            ->getQuery()
            ->getResult();
    }

    //trier par title
    public function findAllOrderByTitle(): array
    {
       
        $query = $this->createQueryBuilder('m')
 
            ->orderBy("m.title", "ASC")
          
            ->setMaxResults(10)
        
            ->getQuery();

        return     $query->getResult();
    }


    public function findRandomMovie()
    {
        //  demander la connection à l'entityManager
        $conn = $this->getEntityManager()->getConnection();

        //  faire la requete SQL

        $sql = "SELECT *
        FROM `movie`
        ORDER BY RAND()
        LIMIT 1";            
        // on execute la requete
        $resultSet = $conn->executeQuery($sql);
        // on ne peux pas utiliser doctrine, donc on fournit un tableau associatif
        return $resultSet->fetchAssociative();
    }
}