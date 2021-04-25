<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    // /**
    //  * @return Transaction[] Returns an array of Transaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

     /**
      * @return Transaction[] Returns an array of Transaction objects
      */
    public function findByUserAndCompte($uid, $cid)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.compte','ct')
            ->leftJoin('t.compteRetrait','ctr')
            ->andWhere('ct.id = :val')
            ->orWhere('ctr.id = :val')
            ->setParameter('val', $cid)
            ->leftJoin('t.sender','sd')
            ->andWhere('sd.id = :uid')
            ->leftJoin('t.withdrawer','wd')
            ->orWhere('wd.id = :uid')
            ->setParameter('uid', $uid)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCompteAll($idCmpte){
        return $this->createQueryBuilder('t')
            ->leftJoin('t.compte','ct')
            ->leftJoin('t.compteRetrait','ctr')
            ->andWhere('ct.id = :val')
            ->orWhere('ctr.id = :val')
            ->setParameter('val', $idCmpte)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCompteDepot($uid, $cid){
        return $this->createQueryBuilder('t')
        ->leftJoin('t.compte','ct')
        ->andWhere('ct.id = :val')
        ->setParameter('val', $cid)
        ->leftJoin('t.sender','sd')
        ->andWhere('sd.id = :uid')
        ->setParameter('uid', $uid)
        ->orderBy('t.id', 'ASC')
        ->getQuery()
        ->getResult()
        ;
    }

    public function findCompteRetrait($uid, $cid){
        return $this->createQueryBuilder('t')
            ->leftJoin('t.compteRetrait','ctr')
            ->andWhere('ctr.id = :val')
            ->setParameter('val', $cid)
            ->leftJoin('t.withdrawer','wd')
            ->andWhere('wd.id = :uid')
            ->setParameter('uid', $uid)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCompteAllDepot($idCmpte){
        return $this->createQueryBuilder('t')
            ->leftJoin('t.compte','ct')
            ->andWhere('ct.id = :val')
            ->setParameter('val', $idCmpte)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCompteAllRetrait($idCmpte){
        return $this->createQueryBuilder('t')
            ->leftJoin('t.compteRetrait','ctr')
            ->andWhere('ctr.id = :val')
            ->setParameter('val', $idCmpte)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    

    /*
    public function findOneBySomeField($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findOneByCode($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.code = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
