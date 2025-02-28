<?php

namespace App\Form;

use App\Entity\Association;
use App\Entity\Don;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;



class DonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant', NumberType::class, [
                'label' => 'Montant du don',
                'required' => true,
                'html5' => false, // Désactive la validation HTML5
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1, // Montant minimum
                    'step' => 0.01, // Permet les montants décimaux
                ],
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'don',
            ]);

        // Champ association (uniquement en mode édition)
        if ($options['is_edit']) {
            $builder->add('association', EntityType::class, [
                'label' => 'Association',
                'class' => Association::class,
                'choice_label' => 'nom', // Affiche le nom de l'association
                'required' => true,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Don::class,
            'is_edit' => false, // Option pour déterminer si le formulaire est en mode édition
        ]);
    }
}
