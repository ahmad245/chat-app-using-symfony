<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\AccountType;
use App\Event\UserRegisterEvent;
use App\Form\UpdatePasswordType;
use App\Security\TokenGenerator;
use App\Repository\RoleRepository;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use App\Security\UserConfirmationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class AccountController extends AbstractController
{
    /**
     * @var PublisherInterface
     */
    private $publisher;

 

        private $em;
        public function __construct(EntityManagerInterface $em , PublisherInterface $publisher) {
            $this->em = $em;
            $this->publisher = $publisher;
         
        }
        /**
         * @Route("/login", name="account_login")
         */
        public function login(AuthenticationUtils $util,SerializerInterface $serializer,Request $req)
        {
            
            $error = $util->getLastAuthenticationError();
           
          
           

        
    
            return $this->render('account/login.html.twig',['error' => $error]);
        }
    
        /**
         * @Route("/logout", name="account_logout")
         *
         * @return void
         */
        public function logout(){
     
           
          
        }
    
        /**
         * @Route("/register",name="account_register")
         */
    
        public function register(Request $req,UserPasswordEncoderInterface $encode,RoleRepository $repoRole){
         $user=new User();
            $form=$this->createForm(AccountType::class,$user);
            $form->handleRequest($req);
            if ($form->isSubmitted() && $form->isValid() ) {
                $role=$repoRole->findOneBy(['name'=>'ROLE_USER']);
                $user->addUserRole($role);
               $user->setPassword($encode->encodePassword($user,$user->getPassword()));
               
                $this->em->persist($user);
                $this->em->flush();
                
               
    
                return $this->redirectToRoute('account_login');
            }
            return $this->render('account/register.html.twig',[
                'form'=>$form->createView()
            ]);
        }

          /**
         * @Route("/checkLogout",name="account_checkLogout")
         */
        public function checkLogout(SerializerInterface $serializer){
           $message=["online"=>false,"lastActive"=>$this->getUser()->getLastActivityAt(),"email"=>$this->getUser()->getUsername()];
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

           return $this->redirectToRoute("account_logout");
        }

      
    
         
    
        
    }