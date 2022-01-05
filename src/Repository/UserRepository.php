<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserSearch;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
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
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em,ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
        $this->registry = $registry;
        $this->em = $em;
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



        if (($search->getType() > -1) && ($search->getType() < 3)) {
            $query->andWhere('u.type = :val_type')
                ->setParameter('val_type', $search->getType() );
        }


        if ($search->getRoles()) {
            $role = $search->getRoles();
            $role = '"' . $role . '"';
            $query->andWhere('JSON_CONTAINS (u.roles, :role) = 1')
                ->setParameter('role',$role);
        }

        return $query->getQuery() ;
    }

    public function findOrCreateFromOauth(ResourceOwnerInterface $owner)
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.facebookID = :facebookId')
            ->setParameter('facebookId', $owner->getId())
            ->getQuery()->getSingleResult();

        if ($user) {
            return $user;
        }

        $user = new User();
        $user->setFacebookID($owner->getId());
        $user->setEmail($owner->getEmail());
        $user->setType(1);


        $url = $owner->getPictureUrl();
        $name = $owner->getName();
        $newFilename = $this->formatLogoName($name);
        $user->setLogo($newFilename);
        file_put_contents($this->getParameter('products_directory') . '/' . $newFilename,file_get_contents($url));

        $user->setRoles(['ROLE_USER']);
//        $user->setMobilePhone();
//        $user->setPassword();
//        $user->setFacebookToken();

            $this->em->persist($user);
            $this->em->flush();

            return  $user;


    }



        private function formatLogoName(String $name)
        {
            $slug = new Slugify();
            return  $slug->slugify($name) . '-' . uniqid() ;
        }

}
