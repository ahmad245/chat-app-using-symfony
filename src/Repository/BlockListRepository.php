<?php

namespace App\Repository;

use App\Entity\BlockList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlockList|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlockList|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlockList[]    findAll()
 * @method BlockList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockList::class);
    }

    public function getAllparticipantIdsInBlockList($id){
        $query=$this->createQueryBuilder('b')
                    ->select('p.id')
                    ->join('b.participant','p')
                    ->where('b.user = :me')
                    ->setParameter('me',$id);
                    return $query->getQuery()->getArrayResult();
                    
    }

    public function findByUserIds($ids){
        $query=$this->createQueryBuilder('b')     
                     ->select('u.email')
                     ->join('b.user','u')             
                    ->where('u IN (:users)')
                    ->setParameter('users',$ids);
                    return $query->getQuery()->getResult();
                    
    }
    public function findByUserId($id){
        $query=$this->createQueryBuilder('b')     
                     ->select('b.id as blockId,p.id as participant,c.id as conv,pu.email,pu.firstName,pu.lastName,pu.id ')
                     ->join('b.user','u')  
                     ->join('b.participant','p') 
                     ->join('p.conversation','c') 
                     ->join('p.user','pu')          
                    ->where('u = :user')
                    ->setParameter('user',$id);
                    return $query->getQuery()->getResult();
                    
    }

    // /**
    //  * @return BlockList[] Returns an array of BlockList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BlockList
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
