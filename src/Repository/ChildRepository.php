<?php

namespace App\Repository;

use App\Entity\Child;
use App\Entity\UserSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Child|null find($id, $lockMode = null, $lockVersion = null)
 * @method Child|null findOneBy(array $criteria, array $orderBy = null)
 * @method Child[]    findAll()
 * @method Child[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChildRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Child::class);
    }


    public function findAllBySchoolId(int $id)
    {
        return $this->createQueryBuilder('c')
            ->where('c.school = :val ')
            ->setParameter('val',$id)

            ->getQuery()
            ->getResult()
            ;

    }

    /**
     * @param int|null $ScholarLevel
     * @return int|mixed|string
     */
    public function findAllByScholarLevel(?int $schoolarLevel)
    {
        return $this->createQueryBuilder('c')
            ->where('c.schoolar_level = :value_level ')
            ->setParameter('value_level',$schoolarLevel)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllBySearch(UserSearch $search)
    {
        $query = $this->createQueryBuilder('u');

        if($search->getScholarLevel() > -1) {
            $query->andWhere('u.schoolar_level = :value_school_level')
                ->setParameter('value_school_level',$search->getScholarLevel());
        }

        if ($search->getRelatedSchool()) {
              $query->andWhere('u.school = :value_school')
              ->setParameter('value_school',$search->getRelatedSchool()->getId());
        }

        return $query->getQuery();

    }

    public function findChildUserByName(String $name,int $parent)
    {
        return $this->createQueryBuilder('c')
            ->where('c.name = :name_child')
            ->setParameter('name_child',$name)
            ->andWhere('c.parent = :parent_id')
            ->setParameter('parent_id',$parent)
            ->getQuery()
            ->getResult()
            ;
    }
}
