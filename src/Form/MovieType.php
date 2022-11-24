<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, 
            [
                "label" => "Titre du film :",
                "attr" => [
                    "placeholder" => "saisissez le titre du film...",
                    "tagada" => 'inca'
                ]
            ])
          
            ->add('duration', RangeType::class, [
                'label' => 'Durée en minutes',
                'attr' => [
                    'min' => 15,
                    'max' => 300
                ]
            ])
           
            ->add('type', ChoiceType::class,
            [
                "placeholder" => "Type",
                "label" => false,
                "expanded" => true,
                "multiple" => false,
                "choices" => [
                    "film" => 'film',
                    "serie" => 'serie'
                ]
            ])
            ->add('releaseDate', DateType::class, [
               
                'widget' => 'single_text'
                ]
            )
            ->add('summary', TextareaType::class)
            ->add('synopsis', TextareaType::class)
            ->add('country', CountryType::class, 
            [
                "label" => "Pays d'origine :"
            ])
           
            ->add('genres', EntityType::class, 
            [
               
                'class' => Genre::class,
        
                'choice_label' => 'name',
          
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
