<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class AddEditQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('TextQuestion', TextType::class, [
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
            ->add('ResponseProp1', TextType::class, [
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
            ->add('ResponseProp2', TextType::class, [
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
            ->add('ResponseProp3', TextType::class, [
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
            ->add('ResponseCorrect', TextType::class, [
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
            ->add('quiz', EntityType::class, [
                'class' => Quiz::class,
                'choice_label' => 'idQuiz',
            ])
            ->add('save', SubmitType::class,['label'=>'click'])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
