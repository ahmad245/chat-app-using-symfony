<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Conversation;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParticipantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/messages", name="messages.")
 */
class MessageController extends AbstractController
{

    const ATTRIBUTES_TO_SERIALIZE = ['id', 'content', 'createdAt', 'mine'];

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var MessageRepository
     */
    private $messageRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

     /**
     * @var PublisherInterface
     */
    private $publisher;

    public function __construct(EntityManagerInterface $entityManager,
                                MessageRepository $messageRepository,
                                UserRepository $userRepository ,
                                ParticipantRepository $participantRepository,
                                PublisherInterface $publisher)
    {
        $this->entityManager = $entityManager;
        $this->messageRepository = $messageRepository;
        $this->userRepository = $userRepository;
        $this->participantRepository = $participantRepository;
        $this->publisher = $publisher;
    }

    /**
     * @Route("/{id}", name="getMessages", methods={"GET"})
     * @param Request $request
     * @param Conversation $conversation
     * @return Response
     */
    public function index(Request $request, Conversation $conversation)
    {
        // can i view the conversation

        $this->denyAccessUnlessGranted('view', $conversation);

        $messages = $this->messageRepository->findMessageByConversationId(
            $conversation->getId()
        );

        /**
         * @var $message Message
         */
        array_map(function ($message) {
            $message->setMine(
                $message->getUser()->getId() === $this->getUser()->getId()
                    ? true : false
            );
        }, $messages);


        return $this->json($messages, Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);
    }

    /**
     * @Route("/{id}", name="newMessage", methods={"POST"})
     * @param Request $request
     * @param Conversation $conversation
     * @return JsonResponse
     * @throws \Exception
     */
    public function newMessage(Request $request, Conversation $conversation,SerializerInterface $serializer)
    {
        $user = $this->getUser();
       
       // $user = 2;
        $recipient = $this->participantRepository->findParticipantByConverstionIdAndUserId(
            $conversation->getId(),
            $user->getId()
        );
       

        // TODO: put everything back
      
       
       // $user = $this->getUser();
      // dd($request->getContent());die;
        $content = json_decode ($request->getContent(),true);
      //  var_dump($content);die;
        
        $message = new Message();
        $message->setContent($content['content']);
       // $message->setUser($this->userRepository->findOneBy(['id' => 2]));
        $message->setUser($user);
       

        $conversation->addMessage($message);
        $conversation->setLastMessage($message);

        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($message);
            $this->entityManager->persist($conversation);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
        $message->setMine(false);
        $messageSerialized = $serializer->serialize($message, 'json', [
            'attributes' => ['id', 'content', 'createdAt', 'mine', 'conversation' => ['id'],"user"=>["id","email","firstName"]]
        ]);
        $update = new Update(
            [
              //  sprintf("/conversations/%s", $conversation->getId()),
                sprintf("/conversations/%s", $recipient->getUser()->getUsername()),
            ],
            $messageSerialized,
            false
            
            // [
            //     sprintf("/%s", $recipient->getUser()->getUsername())
            // ]
        );
        $message->setMine(true);

        $this->publisher->__invoke($update);

        return $this->json($message, Response::HTTP_CREATED, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);
    }
}