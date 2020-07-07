<?php

namespace App\Repository;

use App\Entity\Conversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    // /**
    //  * @return Conversation[] Returns an array of Conversation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Conversation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findConversationByParticipants(int $otherUserId, int $myId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            //->select($qb->expr()->count('p.conversation') )
            ->select('count(p.conversation) as total' )
            ->innerJoin('c.participants', 'p')
            ->where('p.user = :me')
           
            ->orWhere('p.user = :otherUser')
              ->groupBy('p.conversation')
            ->having(
                $qb->expr()->eq(
                    $qb->expr()->count('p.conversation'),
                    2
                )
            )
            ->setParameters([
                'me' => $myId
                ,
             'otherUser' => $otherUserId
            ])
        ;

      //  dd( $qb->getQuery()->getResult());

        return $qb->getQuery()->getResult();
    }

    public function getAllConveersationIds($id){
        $query=$this->createQueryBuilder('c')
                    ->select('c')
                    ->join('c.participants','p')
                    ->where('p.user = :me')
                    ->setParameter('me',$id);
                    return $query->getQuery()->getResult();
                    
    }
      public function findConversationsByUser(int $userId){
          $ids=$this->getAllConveersationIds($userId);
           $query=$this->createQueryBuilder('c')
           ->select('c.id as conversationId ,otherUser.email,otherUser.lastActivityAt,otherUser.id,lm.content,lm.createdAt')
                       ->join('c.participants','p')
                        ->leftJoin('c.lastMessage', 'lm')
                       ->innerJoin('p.user', 'otherUser')
                       ->where('p.user != :me')
                       ->andWhere('p.conversation IN (:ids)')
                       ->setParameters([
                           'me'=>$userId,
                           'ids'=>$ids
                       ])
                     ->orderBy('lm.createdAt', 'DESC');
                       return $query->getQuery()->getResult();   

    }

    // public function findConversationsByUser(int $userId)
    // {
    //     $qb = $this->createQueryBuilder('c');
    //     $qb->
    //         select('otherUser.email', 'c.id as conversationId', 'lm.content', 'lm.createdAt')
    //         ->innerJoin('c.participants', 'p', Join::WITH, $qb->expr()->neq('p.user', ':user'))
    //         ->innerJoin('c.participants', 'me', Join::WITH, $qb->expr()->eq('me.user', ':user'))
    //         ->leftJoin('c.lastMessage', 'lm')
    //         ->innerJoin('me.user', 'meUser')
    //         ->innerJoin('p.user', 'otherUser')
    //         ->where('meUser.id = :user')
    //         ->setParameter('user', $userId)
    //         ->orderBy('lm.createdAt', 'DESC')
    //     ;
        
    //     return $qb->getQuery()->getResult();
    // }

    public function checkIfUserisParticipant(int $conversationId, int $userId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->innerJoin('c.participants', 'p')
            ->where('c.id = :conversationId')
            ->andWhere(
                $qb->expr()->eq('p.user', ':userId')
            )
            ->setParameters([
                'conversationId' => $conversationId,
                'userId' => $userId
            ])
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}
