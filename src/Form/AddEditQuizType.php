<?php

namespace App\Form;

use App\Entity\ContenuMultiMedia;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AddEditQuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('TitreQuiz', TextType::class, [
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
            ->add('DateQuiz', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'data' => new \DateTime(),
                'constraints' => [
                    new Assert\NotNull([
                        'message' => 'Veuillez choisir une date.',
                        'groups' => ['Default'], // Gérer selon le contexte
                    ]),
                ],
            ])
            
            
            ->add('ScoreQuiz')
            ->add('ReponseChoisit', TextType::class, [
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
            ->add('contenuMultiMedia', EntityType::class, [
                'class' => ContenuMultiMedia::class,
                'choice_label' => 'idContenu',
            ])
            ->add('save', SubmitType::class,['label'=>'click'])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
