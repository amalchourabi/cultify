<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddEditQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('TextQuestion')
            ->add('ResponseProp1')
            ->add('ResponseProp2')
            ->add('ResponseProp3')
            ->add('ResponseCorrect')
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
