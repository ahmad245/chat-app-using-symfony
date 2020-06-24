<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UpdatePassword;
use App\Form\UpdatePasswordType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ManagePasswordController extends AbstractController
{
    private $em;
    private $encode;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encode
       
        )
    {
        $this->em=$em;
        $this->encode=$encode;

    }
    /**
     * @Route("/managePassword/password-update", name="password_update")
     */
    public function updatePassword(Request $req)
    {
        $password=new UpdatePassword();
        $user=$this->getUser();
        $form=$this->createForm(UpdatePasswordType::class,$password);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {
           if(!password_verify($password->getOldPassword(),$user->getPassword()))
           {
            $form->get('oldPassword')->addError(new FormError('password erorr'));
           }
           else{
               $user->setPassword($this->encode->encodePassword($user,$password->getNewPassword()));
               $this->em->persist($user);
               $this->em->flush();
               return $this->redirectToRoute('home');

           }
        }
        return $this->render('manage_password/updatePassword.html.twig', [
            'form' => $form->createView()
        ]);

    }

}
