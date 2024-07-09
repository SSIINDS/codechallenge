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

    //    /**
    //     * @return Customers[] Returns an array of Customers objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

       public function getCustomers($id = null)
       {
            $q = $this->createQueryBuilder('u');
            $q->select('u.id, CONCAT(u.FirstName, \' \', u.LastName) as FullName, u.Email, u.Country');
            if($id){
                $q->select('u.id, CONCAT(u.FirstName, \' \', u.LastName) as FullName, u.Email, u.Country, u.Username, u.Gender, u.City, u.Phone');
                $q->andWhere('u.id = :id');
                $q->setParameter('id', $id);
            }
            return $q->getQuery()->getArrayResult();
       }
}
