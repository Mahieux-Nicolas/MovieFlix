<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, 
            [
                "label" => "Votre Pseudo :",
                "attr" => [
                    "placeholder" => "saisissez votre pseudo ..."
                ]
            ])
            ->add('email', EmailType::class, 
            [
                "label" => "Votre E-Mail :",
                "attr" => [
                    "placeholder" => "saisissez votre email ..."
                ]
            ])
            ->add('content', TextareaType::class, 
            [
                "label" => "Critique :",
            ])
            ->add('rating', ChoiceType::class, [
                'placeholder' => 'Votre avis ?',
             
                "label" => false,
     
                'multiple' => false,
                'expanded' => false,
     
                'choices'  => [
                    'Excellent' => 5,
                    'Très bon' => 4,
                    'Bon' => 3,
                    'Peut mieux faire' => 2,
                    'A éviter' => 1
                ],
                
                'preferred_choices' => [3, 1]
                ])
            ->add('reactions', ChoiceType::class,[
                "label" => "Ce film vous a fait :",
               
                'multiple' => true,
                'expanded' => true,
                
                'choices' => [
                    '😭' => "cry",
                    '😊' => "smile",
                    '🤔' => "think",
                    '💭' => "dream",
                    '😴' => "sleep",
                ]])
            
            ->add('watchedAt', DateType::class, [
               
                'widget' => 'single_text',

                'input' => 'datetime_immutable',
             
            ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
