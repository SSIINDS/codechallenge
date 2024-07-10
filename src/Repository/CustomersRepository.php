<?php

namespace App\Repository;

use App\Entity\Customers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customers>
 */
class CustomersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customers::class);
    }

    public function getCustomers($id = null)
    {
        $q = $this->createQueryBuilder('u');
        $q->select('u.id, CONCAT(u.FirstName, \' \', u.LastName) as FullName, u.Email, u.Country');
        if($id){
            $q->select('u.id, CONCAT(u.FirstName, \' \', u.LastName) as FullName, u.Email, u.Country, u.Username, u.Gender, u.City, u.Phone');
            $q->andWhere('u.id = :id');
            $q->setParameter('id', $id);
            return $q->setMaxResults(1)->getQuery()->getOneOrNullResult();
        }
        return $q->getQuery()->getArrayResult();
    }
}
