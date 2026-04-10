<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.role = :role')
            ->setParameter('role', $role)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllCandidats(): array
    {
        return $this->findByRole(User::ROLE_CANDIDAT);
    }

    public function findAllEmployes(): array
    {
        return $this->findByRole(User::ROLE_EMPLOYE);
    }

    public function findAllRH(): array
    {
        return $this->findByRole(User::ROLE_RH);
    }
}
