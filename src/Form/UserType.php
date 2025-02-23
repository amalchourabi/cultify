<?php
// src/Form/UserType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File; // Add this line

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Last Name',
                'attr' => ['placeholder' => 'Enter your last name'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'First Name',
                'attr' => ['placeholder' => 'Enter your first name'],
            ])
            ->add('username', TextType::class, [
                'label' => 'Username',
                'attr' => ['placeholder' => 'Enter your username'],
            ])
            ->add('num_tel', TextType::class, [
                'label' => 'Phone Number',
                'attr' => ['placeholder' => 'Enter your phone number (8 digits)'],
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Enter your email'],
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Gender',
                'choices' => [
                    'Male' => 'male',
                    'Female' => 'female',
                ],
                'placeholder' => 'Choose your gender',
                'required' => false,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'attr' => ['placeholder' => 'Enter your password'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('MontantApayer', NumberType::class, [
                'label' => 'Amount to Pay',
                'attr' => ['placeholder' => 'Enter the amount'],
                'required' => false,
            ])
            ->add('Datedenaissance', DateType::class, [
                'label' => 'Date of Birth',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['placeholder' => 'Enter your birth date'],
            ])
            ->add('profilepicture', FileType::class, [
                'label' => 'Profile Picture',
                'mapped' => false, // This field is not mapped to the entity
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, GIF).',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}