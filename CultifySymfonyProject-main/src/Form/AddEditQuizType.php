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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

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
                    new Assert\LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date ne peut pas être dans le futur.',
                    ]),
                ],
            ])
            
            
            ->add('ScoreQuiz', IntegerType::class, [
                'data' => 0, // Valeur par défaut
                'attr' => ['readonly' => true], // Empêche l'utilisateur de modifier
                'constraints' => [
                    new Assert\NotNull(['message' => 'Le score est obligatoire.']),
                    new Assert\EqualTo(['value' => 0, 'message' => 'Le score doit être 0.']),
                ],
            
            ])
            ->add('ReponseChoisit', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^$/',
                        'message' => 'Le champ doit être une chaîne vide.',
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
