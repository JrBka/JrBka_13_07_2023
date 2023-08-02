<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'attr'=>[
                    'class'=> 'form-control',
                    'style'=>'margin-bottom: 15px'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=>'Titre',
                'required'=>true
            ])
            ->add('content',TextareaType::class,[
                'attr'=>[
                    'class'=> 'form-control',
                    'style'=>'margin-bottom: 15px'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=>'Contenu',
                'required'=>true
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
