<?php
namespace App\Form;

use App\Entity\Reponse;
use App\Entity\Reclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
class AddEditReponseType extends AbstractType

{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateReponse', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de réponse',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez un titre'],
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Décrivez la réponse...'],
            ])
            ->add('offre', TextType::class, [
                'label' => 'Offre',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez une offre'],
            ])
            ->add('reclamation', EntityType::class, [
                'label' => 'Réclamation associée',
                'class' => Reclamation::class,
                'choice_label' => 'id', // Afficher l'ID de la réclamation
                'attr' => ['class' => 'form-control'],
            ])
            ->add('piece_jointe', FileType::class, [
                'label' => 'Pièce jointe (optionnelle)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'application/pdf'],
                        'mimeTypesMessage' => 'Seuls les fichiers PDF, JPG et PNG sont autorisés.',
                    ]),
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
        ]);
    }
}