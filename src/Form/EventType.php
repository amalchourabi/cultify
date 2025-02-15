<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('lieu')
            ->add('date_e', null, [
                'widget' => 'single_text',
            ])
            ->add('organisation')
            ->add('capacite')
            ->add('nbplaces')
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Théâtre' => 'Théâtre',
                    'Musique' => 'Musique',
                    'Sport' => 'Sport',
                    'Musée' => 'Musée',
                    'Gastronomie' => 'Gastronomie',
                ],
                'expanded' => false,  // Menu déroulant
                'multiple' => false,  // Un seul choix possible
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
