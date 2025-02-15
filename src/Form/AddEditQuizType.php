<?php

namespace App\Form;

use App\Entity\ContenuMultiMedia;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddEditQuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('TitreQuiz')
            ->add('DateQuiz', null, [
                'widget' => 'single_text',
            ])
            ->add('ScoreQuiz')
            ->add('ReponseChoisit')
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
