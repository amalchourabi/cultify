<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('date_r', null, [
                'widget' => 'single_text',
            ])
            ->add('theme')
            ->add('url')
            ->add('nbTickets', ChoiceType::class, [
                'choices' => [
                    '1 Ticket' => 1,
                    '2 Tickets' => 2,
                    '3 Tickets' => 3,
                    '4 Tickets' => 4,
                    '5 Tickets' => 5,
                ],
                'expanded' => false,  // Menu déroulant
                'multiple' => false,  // Un seul choix possible
            ])
            ->add('id_e', HiddenType::class, [
                'data' => $options['id_e'],  // Pré-rempli avec l'ID de l'événement
                'mapped' => false,            // Non lié directement à l'entité
            ])
           ;
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'id_e' => null, // Permet de passer l'ID de l'événement au formulaire
        ]);
    }
}
