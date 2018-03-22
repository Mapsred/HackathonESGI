<?php

namespace App\Repository;

use App\Entity\ProfileLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProfileLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfileLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfileLink[]    findAll()
 * @method ProfileLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileLinkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProfileLink::class);
    }

//    /**
//     * @return ProfileLink[] Returns an array of ProfileLink objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProfileLink
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
