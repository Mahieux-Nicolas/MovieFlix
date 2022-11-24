<?php

namespace App\Form;

use App\Entity\Movie;
use App\Entity\Season;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number', NumberType::class)
            ->add('episodesCount', NumberType::class)
            ->add('movie', EntityType::class, [
            
                'class' => Movie::class,
              
                'choice_label' => 'title',
              
                'multiple' => false,
                'expanded' => false,
             
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        // TODO : filtre sur le type = serie
                        ->where("m.type = 'serie'")
                        ->orderBy('m.title', 'ASC');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
