<?php

namespace App\Form;

use App\Entity\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType; 

class AddEditAssociationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description', TextareaType::class, [ // Utilisation de TextareaType
                'attr' => ['rows' => 5], // Définir le nombre de lignes
            ])
            ->add('contact')
            ->add('but', TextareaType::class, [ // Utilisation de TextareaType
                'attr' => ['rows' => 5], // Définir le nombre de lignes
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de l\'association',
                'required' => false,
                'mapped' => false, // Ce champ n'est pas mappé directement à l'entité
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Association::class,
        ]);
    }
}
