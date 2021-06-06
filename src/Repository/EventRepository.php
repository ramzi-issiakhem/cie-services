<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\EventSearch;
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

    public function findAllByState(EventSearch $search) :Query
    {
        $query = $this->createQueryBuilder('e')
            ->orderBy('e.event_datetime','ASC');

        if ($search->getEventDatetime()) {
            $date = $search->getEventDatetime();
            $query->andWhere('e.event_datetime < :val_event')
                ->setParameter('val_event',$date);
        }

        if ($search->getState()) {
            $state = $search->getState();
            $query->andWhere('e.state = :val_state')
                ->setParameter('val_state',$state);
        }
        if ($search->getDeadlineDate()) {
            $deadline = $search->getDeadlineDate();
            $query->andWhere('e.deadline_date < :val_deadline')
            ->setParameter('val_deadline',$deadline);
        }

        if ($search->getSchool()) {
            $id_school = $search->getSchool()->getId();
            $query->andWhere('e.school = :val_school')
                ->setParameter('val_school',$id_school);
        }

        if ($search->getProduct()) {
            $id_product = $search->getProduct()->getId();
            $query->andWhere('e.product = :val_product')
                ->setParameter('val_product',$id_product);
        }
        return $query->getQuery();
    }

    /**
     * @param int|null $getId
     * @return int|mixed|string
     */
   /* public function findAllByChild(?int $getId)
    {

    }*/
}
