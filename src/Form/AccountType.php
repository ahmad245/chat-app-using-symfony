<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class,[
            'attr' => [
                'class' => 'browser-default'
            ],
            
        ])
            ->add('lastName',TextType::class,[
            'attr' => [
                'class' => 'browser-default'
            ],
            
        ])
            ->add('email',TextType::class,[
            'attr' => [
                'class' => 'browser-default'
            ],
            
        ])
            ->add('password',PasswordType::class,[
                'attr' => [
                    'class' => 'browser-default'
                ],
                
            ])
         
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
        ]);
    }
}
