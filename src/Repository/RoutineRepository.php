<?php

namespace App\Repository;

use App\Entity\Profile;
use App\Entity\Routine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Routine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Routine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Routine[] findAll()
 * @method Routine[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoutineRepository extends ServiceEntityRepository
{
    /**
     * RoutineRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Routine::class);
    }

    /**
     * @param Profile $profile
     * @param string $name
     * @return Routine
     */
    public function findOneByProfileAndName(Profile $profile, string $name)
    {
        return $this->findOneBy(['profile' => $profile, 'name' => $name]);
    }
}
