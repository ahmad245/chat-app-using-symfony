<?php

namespace App\Event;

use App\Entity\User;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ActivityListener extends AbstractController implements EventSubscriberInterface

{
    protected $securityContext;
    protected $entityManager;
    private $serializer;
      /**
     * @var PublisherInterface
     */
    private $publisher;

    public function __construct(Security $securityContext, EntityManagerInterface $entityManager,SerializerInterface $serializer, PublisherInterface $publisher)
    {
        $this->securityContext = $securityContext;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->publisher = $publisher;
       
    }
   
    /**
    * Update the user "lastActivity" on each request
    * @param ControllerEvent $event
    */
    public function  onKernelRequest(ControllerEvent $event)
    {
        // Check that the current request is a "MASTER_REQUEST"
        // Ignore any sub-request
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
           
            return;
        }

        // Check token authentication availability
        if ($this->securityContext->getToken()) {
            $user = $this->securityContext->getToken()->getUser();

            if ( ($user instanceof User) && !($user->isActiveNow()) ) {
                $user->setLastActivityAt(new \DateTime());
                $this->entityManager->flush($user);


                $message=["online"=>$this->getUser()->isActiveNow(),"lastActive"=>$this->getUser()->getLastActivityAt(),"email"=>$this->getUser()->getUsername()];
                $messageSerialized = $this->serializer->serialize($message, 'json');
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
            }
        }
    }
    public static function getSubscribedEvents()
    {
        return [
          
            KernelEvents::CONTROLLER => [['onKernelRequest']],
        ];
    }
}