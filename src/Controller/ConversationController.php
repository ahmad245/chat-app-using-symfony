<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Participant;
use App\Entity\Conversation;
use App\Repository\UserRepository;
use Symfony\Component\WebLink\Link;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mercure\Update;


/**
 * @Route("/conversations", name="conversations.")
 */
class ConversationController extends AbstractController
{    const ATTRIBUTES_TO_SERIALIZE = ['id','email','firstName','lastName','lastActivityAt'];
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
     * @var PublisherInterface
     */
    private $publisher;

    public function __construct(UserRepository $userRepository,
                                EntityManagerInterface $entityManager,
ConversationRepository $conversationRepository
, PublisherInterface $publisher)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->conversationRepository = $conversationRepository;
        $this->publisher = $publisher;
    }

   
   
    /**
     * @Route("/", name="getConversations" ,methods={"GET"})
     */
    public function getConvs(Request $req,SerializerInterface $serializer) {
        $conversations = $this->conversationRepository->findConversationsByUser($this->getUser()->getId());
        $hubURL=$this->getParameter('mercure.default_hub');
        $this->addLink($req,new Link('mercure',$hubURL) );

        $message=["online"=>$this->getUser()->isActiveNow(),"lastActive"=>$this->getUser()->getLastActivityAt(),"email"=>$this->getUser()->getUsername()];
            $messageSerialized = $serializer->serialize($message, 'json');
            $update = new Update(
                [
                  //  sprintf("/conversations/%s", $conversation->getId()),
                   '/allUser'
                ],
                $messageSerialized,
                false
                
                // [
                //     sprintf("/%s", $recipient->getUser()->getUsername())
                // ]
            );
         
    
            $this->publisher->__invoke($update);

        return $this->json(['conversations'=>$conversations,'user'=>$this->getUser()->getUsername()]);
    }

     /**
     * @Route("/add/{id}", name="newConversations")
     * @param Request $request
     *  @param User $otherUser
     * @return JsonResponse
     * @throws \Exception
     */
    public function index(Request $request,User $otherUser)
    {
        
        $content = json_decode ($request->getContent(),true);
        if (is_null($otherUser)) {
            throw new \Exception("The user was not found");
        }

        // cannot create a conversation with myself
        if ($otherUser->getId() === $this->getUser()->getId()) {
            throw new \Exception("That's deep but you cannot create a conversation with yourself");
        }

        // Check if conversation already exists
        $conversation = $this->conversationRepository->findConversationByParticipants(
            $otherUser->getId(),
            $this->getUser()->getId()
        );

        if (count($conversation)) {
            throw new \Exception("The conversation already exists");
        }

        $conversation = new Conversation();

        $participant = new Participant();
        $participant->setUser($this->getUser());
        $participant->setConversation($conversation);


        $otherParticipant = new Participant();
        $otherParticipant->setUser($otherUser);
        $otherParticipant->setConversation($conversation);

        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($conversation);
            $this->entityManager->persist($participant);
            $this->entityManager->persist($otherParticipant);

            $this->entityManager->flush();
            $this->entityManager->commit();

        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }


        return $this->json([
            'id' => $conversation->getId(),
            'otheruser'=> $otherUser
        ], Response::HTTP_CREATED, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);
    }
    

    

}