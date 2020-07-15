<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;
use App\Repository\BlockListRepository;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    const ATTRIBUTES_TO_SERIALIZE = [  'id','email','firstName','lastName','participantId'];
      /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ConversationRepository
     */
    private $conversationRepository;
     /**
     * @var BlockListRepository
     */
    private $repoBlockList;

    public function __construct(UserRepository $userRepository,
                                EntityManagerInterface $entityManager,BlockListRepository $repoBlockList,
ConversationRepository $conversationRepository)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->conversationRepository = $conversationRepository;
        $this->repoBlockList = $repoBlockList;
    }

    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * @Route("/user/allUsers",name="allUsers", methods={"GET"})
     */

    public function getAllUser(){
       // $users= $this->userRepository->findAll();
    // $users=$this->userRepository->findAllUserWithoutMe($this->getUser()->getId());
     $users=$this->userRepository->findAllUserWithoutMe($this->getUser());
     $userBlockList=  $this->repoBlockList->findByUserId($this->getUser());
    // dump($userBlockList);die;
      $data=[
          'users'=>$users,
          'userBlockList'=>$userBlockList
      ];
 
        return $this->json($data, Response::HTTP_CREATED, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);
     }

   


}
