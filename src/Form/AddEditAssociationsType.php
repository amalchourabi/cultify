<?php

namespace App\Form;

use App\Entity\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType; 
use Symfony\Component\Form\Extension\Core\Type\NumberType; 
use Symfony\Component\Validator\Constraints as Assert;
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
            ->add('montantDesire', NumberType::class, [
                'label' => 'Montant désiré (TND)',
                'required' => true,
                'html5' => false, // Désactive la validation HTML5
                'attr' => [
                    'class' => 'form-control',
                    'min' => 100, // Montant minimum
                    'step' => 0.01, // Permet les montants décimaux
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de l\'association',
                'required' => true, // Le champ est obligatoire
                'mapped' => false, // Ce champ n'est pas mappé directement à l'entité
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez télécharger une image.',
                    ]),
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG).',
                    ]),
                ],
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
