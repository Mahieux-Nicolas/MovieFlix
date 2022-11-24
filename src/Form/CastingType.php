<?php

namespace App\Form;

use App\Entity\Casting;
use App\Entity\Movie;
use App\Entity\Person;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CastingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role')
            ->add('creditOrder')
            ->add('movie', EntityType::class, 
            [
                'class' => Movie::class,
                'choice_label' => 'title',
                'multiple' => false,
                'expanded' => false
            ])
            ->add('person', EntityType::class, 
            [
                // on précise l'entité liée
                'class' => Person::class,
                // quelle propriété doit être utilisée pour l'affichage
                'choice_label' => 'getFullname',
                //! ne pas oublier le multiple
                'multiple' => false,
                'expanded' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.firstname', 'ASC');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Casting::class,
        ]);
    }
}
