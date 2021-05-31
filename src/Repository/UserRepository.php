<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
        $this->registry = $registry;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }



    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
            */



    public function findAllBySearch(UserSearch $search): Query
    {
        $query = $this->createQueryBuilder('u');

//        if ($search->getScholarLevel()) {
//            $query
//                ->from('Child','c')
//                ->innerJoin();
//            $users[] = $this->child_repository->findAllByScholarLevel($search->getScholarLevel());
//        }

//        if ($search->getRelatedSchool()) {
//            $users[] = $this->child_repository->findAllBySchoolId($search->getRelatedSchool()->getId());
//        }


        if (($search->getType() > -1) && ($search->getType() < 3)) {
            $query->andWhere('u.type = :val_type')
                ->setParameter('val_type', $search->getType() );
        }

        // TODO Gérer le probleme de filtre par role
        if ($search->getRoles()) {
            $role = "ROLE_ADMIN";//$search->getRoles();

            $query->andWhere(' JSON_CONTAINS(u.roles, :role) = 1')
                ->setParameter('role',$role)
                ;
        }

        return $query->getQuery() ;
    }

}
