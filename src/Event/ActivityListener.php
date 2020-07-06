<?php

namespace App\Event;

use App\Entity\User;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ActivityListener implements EventSubscriberInterface
{
    protected $securityContext;
    protected $entityManager;

    public function __construct(Security $securityContext, EntityManagerInterface $entityManager)
    {
        $this->securityContext = $securityContext;
        $this->entityManager = $entityManager;
       
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