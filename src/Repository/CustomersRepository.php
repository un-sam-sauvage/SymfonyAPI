<?php

namespace App\Repository;

use App\Entity\Customers;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customers>
 *
 * @method Customers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customers[]    findAll()
 * @method Customers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Customers::class);
	}


	public function getCustomerFromClient(int $idCustomer, User|null $user) {
		return $this->createQueryBuilder('c')
		->where('c.id = :idCustomer')
		->setParameter('idCustomer', $idCustomer)
		->andWhere('c.client = :user')
		->setParameter('user', $user)
		->getQuery()
		->getOneOrNullResult();
	}

	public function getCustomersFromClient(User|null $user, $page, $limit)  {
		return $this->createQueryBuilder('c')
		->where('c.client = :user')
		->setParameter('user', $user)
		->setFirstResult(($page - 1) * $limit)
		->setMaxResults($limit)
		->getQuery()
		->getResult();
	}

	public function deleteCustomerFromClient (int $idCustomer, User|null $user) {
		return $this->createQueryBuilder('c')
		->delete()
		->where('c.id = :idCustomer')
		->setParameter('idCustomer', $idCustomer)
		->andWhere('c.client = :user')
		->setParameter('user', $user)
		->getQuery()
		->getResult();
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

//    public function findOneBySomeField($value): ?Customers
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
