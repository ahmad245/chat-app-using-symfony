<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisterEvent;
use App\Form\AccountType;
use App\Form\UpdatePasswordType;
use App\Repository\RoleRepository;
use App\Security\TokenGenerator;
use App\Security\UserConfirmationService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    
        private $em;
        public function __construct(EntityManagerInterface $em ) {
            $this->em = $em;
         
        }
        /**
         * @Route("/login", name="account_login")
         */
        public function login(AuthenticationUtils $util)
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

      
    
         
    
        
    }