<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('date_e', null, [
                'widget' => 'single_text',
                'label' => 'Date de l\'événement'
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix',
                'scale' => 2,
                'attr' => ['min' => 0],
            ])
            ->add('organisation')
            ->add('image', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Format d\'image invalide (JPEG, PNG ou GIF uniquement)',
                    ])
                ],
            ])
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
                'label' => 'Catégorie'
            ])
            ->add('latitude', HiddenType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un emplacement sur la carte']),
                    new Range([
                        'min' => -90,
                        'max' => 90,
                        'notInRangeMessage' => 'La latitude doit être entre {{ min }} et {{ max }} degrés'
                    ])
                ]
            ])
            ->add('longitude', HiddenType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un emplacement sur la carte']),
                    new Range([
                        'min' => -180,
                        'max' => 180,
                        'notInRangeMessage' => 'La longitude doit être entre {{ min }} et {{ max }} degrés'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}