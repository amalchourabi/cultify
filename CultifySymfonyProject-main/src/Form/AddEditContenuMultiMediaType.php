<?php

namespace App\Form;

use App\Entity\ContenuMultiMedia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\CallbackTransformer;

class AddEditContenuMultiMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('TitreMedia', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le titre est obligatoire.']),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Le titre doit contenir au moins 3 caractères.',
                    ]),
                ],
            ])
            ->add('TextMedia', TextType::class, [
                'required' => true,
                'attr' => ['placeholder' => 'Entrez un texte'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le texte est obligatoire.']),
                    new Assert\Length([
                        'min' => 10,
                        'max' => 500,
                        'minMessage' => 'Le texte doit contenir au moins 10 caractères.',
                    ]),
                ],
            ])
            ->add('photoMedia', FileType::class, [
                'label' => 'Image du média',
                'mapped' => false, // Empêche Doctrine d’essayer de sauvegarder directement le fichier
                'required' => false,
                'constraints' => [
                    new Assert\Image([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide.',
                    ]),
                ],
            ])
            ->add('CategorieMedia', ChoiceType::class, [
                'choices' => [
                    'Musique' => 'musique',
                    'Film' => 'film',
                    'Documentaire' => 'documentaire',
                    'Série' => 'serie',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('DateMedia', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'data' => new \DateTime(),
                'constraints' => [
                    new Assert\NotNull([
                        'message' => 'Veuillez choisir une date.',
                        'groups' => ['Default'], // Gérer selon le contexte
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date ne peut pas être dans le futur.',
                    ]),

                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer']);

        // Transformer `CategorieMedia` (DB <-> Form)
        $builder->get('CategorieMedia')
            ->addModelTransformer(new CallbackTransformer(
                function ($string) { return $string ? explode('|', $string) : []; },
                function ($array) { return implode('|', $array); }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContenuMultiMedia::class,
        ]);
    }
}
