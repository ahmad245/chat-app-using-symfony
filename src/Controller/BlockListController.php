<?php
namespace App\Controller;
use App\Entity\User;
use App\Entity\BlockList;
use App\Entity\Participant;
use App\Entity\Conversation;
use App\Repository\UserRepository;
use Symfony\Component\WebLink\Link;
use Symfony\Component\Mercure\Update;
use App\Repository\BlockListRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


  /**
 * @Route("/blocklist", name="blocklist.")
 *
 */

class BlockListController extends AbstractController
{
    const ATTRIBUTES_TO_SERIALIZE = ['id','email','firstName','lastName','lastActivityAt'];
    /**
     * @var UserRepository
     */
    private $userRepository;
     /**
     * @var BlockListRepository
     */
    private $repoBlockList;
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

    public function __construct(BlockListRepository $userRepository,
                                EntityManagerInterface $entityManager,
ConversationRepository $conversationRepository
, PublisherInterface $publisher,BlockListRepository $repoBlockList)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->conversationRepository = $conversationRepository;
        $this->publisher = $publisher;
        $this->repoBlockList = $repoBlockList;
    }
    /**
     * @Route("/", name="block_list", methods={"GET"})
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $blockList= $this->repoBlockList->findByUserId($this->getUser());

        return $this->json($blockList);
    }

    /**
     * @Route("/add/{id}", name="block_list_add" )
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function add(Request $request,Participant $participant)
    {
        // $data = $request->getContent();
        // $data = json_decode($data, true);
        
        $content = json_decode ($request->getContent(),true);
        $blockList=new BlockList();
        $blockList->setUser($this->getUser());
        $blockList->setParticipant($participant);
        $this->entityManager->persist($blockList);
        $this->entityManager->flush();


       
        return $this->json($blockList->getId(), Response::HTTP_CREATED,['groups'=>['block']]);
        // return $this->render('block_list/index.html.twig', [
        //     'controller_name' => 'BlockListController',
        // ]);
    }
     /**
     * @Route("/remove/{id}", name="block_list_remove", methods={"POST"})
     */
    public function remove(BlockList $block)
    {
        $this->entityManager->remove($block);
        $this->entityManager->flush();
        return $this->json('the coversation was removed',200,[],[]);
    }
}
