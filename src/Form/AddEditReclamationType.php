<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class AddEditReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de réclamation',
                'choices' => [
                    'Récupérer mon compte' => 'Récupérer mon compte',
                    'Problème de réservation' => 'Problème de réservation',
                    'Problème dans l\'encyclopédie' => 'Problème dans l\'encyclopédie',
                    'Problème de don' => 'Problème de don',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez un titre'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Décrivez votre réclamation...'],
                
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email (optionnel)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez votre email'],
                
            ])
            ->add('priorite', ChoiceType::class, [
                'label' => 'Priorité',
                'choices' => [
                    'Urgent' => 'Urgent',
                    'Normale' => 'Normale',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('piece_jointe', FileType::class, [
                'label' => 'Pièce jointe (optionnelle)',
                'mapped' => false, 
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
