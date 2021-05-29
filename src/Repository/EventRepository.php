<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAllOrderByState(String $type)
    {
        return $this->createQueryBuilder('e')
            ->orderBy("e.state",$type)
            ->getQuery()
            ->getResult();
    }

//    public function findAllOrderByEventDate(String $type)
//    {
//        return $this->createQueryBuilder('e')
//            ->orderBy("e.event_date",$type)
//            ->getQuery()
//            ->getResult();
//    }
//
//    public function findAllOrderByDeadLineDate(String $type)
//    {
//        return $this->createQueryBuilder('e')
//            ->orderBy("e.deadline_datetime",$type)
//            ->getQuery()
//            ->getResult();
//    }

    public function findAllByState(int $state,String $order) :Query
    {
        return $this->createQueryBuilder('e')
            ->orderBy("e.event_datetime",$order)
            ->where('e.state = :etat')
            ->setParameter('etat', $state)
            ->getQuery();
    }
}
