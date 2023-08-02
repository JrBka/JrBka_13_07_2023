<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    private $security;
    private $admin;

    public function __construct(Security $security){
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if ($this->security->getUser() && $this->security->getUser()->getRoles() == ['ROLE_ADMIN']){
            $this->admin = true;
        }else{
            $this->admin = false;
        }

        $builder
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur",
                'required'=>true,
                ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required'=> $this->admin ? false : true,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'first_options'  => ['label' => 'Mot de passe', 'required' => $this->admin ? false : true],
                'second_options' => ['label' => 'Tapez le mot de passe à nouveau', 'required' => $this->admin ? false : true],
                'constraints' => new Assert\Regex(['pattern' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?([^\w\s]|[_])).{8,}$/',
                    'match' => true,
                    'message' => 'Votre mot de passe doit comporter au moins huit caractères, dont des lettres majuscules et minuscules, un chiffre et un symbole',

                ])
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'required'=>true,
                ])
            ->addEventListener(FormEvents::PRE_SET_DATA,function (FormEvent $event){
                $form = $event->getForm();
                if ($this->security->getUser() && $this->admin){
                $form->add('roles',EntityType::class, [
                    'class'=>Role::class,
                    'label'=> 'Role',
                    'multiple'=>true,
                    'mapped'=>false,
                    'required'=>false,
                ]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
