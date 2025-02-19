<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('nbTickets')
            ->add('id_e', HiddenType::class, [
                'data' => $options['id_e'],  // Pré-rempli avec l'ID de l'événement
                'mapped' => false,            // Non lié directement à l'entité
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix',
                'scale' => 2, // Nombre de décimales
                'attr' => ['min' => 0], // Empêche les valeurs négatives
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Confirmé' => 'confirmé',
                    'Annulé' => 'annulé',
                ],
                'expanded' => true,  // Affiche sous forme de boutons radio
                'multiple' => false, // Un seul choix possible
                'label' => 'État de la réservation',
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
