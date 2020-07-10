<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;

class UserController extends AbstractController
{
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

    public function __construct(UserRepository $userRepository,
                                EntityManagerInterface $entityManager,
ConversationRepository $conversationRepository)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->conversationRepository = $conversationRepository;
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
 
        return $this->json(['users'=>$users],200,[],['groups'=>['users']]);
     }

   


}
