<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\File; // Add this line

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Last Name',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your last name.']),
                    new Length(['min' => 2, 'max' => 255]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'First Name',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your first name.']),
                    new Length(['min' => 2, 'max' => 255]),
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Username',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a username.']),
                    new Length(['min' => 3, 'max' => 255]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter an email.']),
                    new Email(['message' => 'Please enter a valid email address.']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false, // This ensures the field is not directly mapped to the entity
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a password.']),
                    new Length(['min' => 6, 'max' => 255]),
                ],
            ])
            ->add('password', PasswordType::class, [ // Fixed field name
                'label' => 'Password',
                'attr' => ['placeholder' => 'Confirmer password'],
            ])
            ->add('num_tel', TextType::class, [
                'label' => 'Phone Number',
                'constraints' => [
                    new Length(['max' => 8, 'maxMessage' => 'Phone number cannot exceed 8 digits.']),
                ],
                'required' => false,
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Gender',
                'choices' => [
                    'Male' => 'male',
                    'Female' => 'female',
                ],
                'expanded' => true, // Radio buttons
                'multiple' => false,
                'constraints' => [
                    new Choice(['choices' => ['male', 'female'], 'message' => 'Please select a valid gender.']),
                ],
            ])
            ->add('Datedenaissance', DateType::class, [
                'label' => 'Date of Birth',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your date of birth.']),
                ],
            ])
            ->add('profilepicture', FileType::class, [
                'label' => 'Profile Picture',
                'mapped' => false, // This field is not mapped to the entity
                'required' => false, // Make it optional
                'constraints' => [
                    new File([
                        'maxSize' => '1024k', // 1MB max size
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'], // Allowed file types
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, GIF).',
                    ]),
                ],
            ])
            ->add('MontantApayer', NumberType::class, [
                'label' => 'Amount to Pay',
                'required' => false,
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(['message' => 'You must agree to our terms.']),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                    'Organisateur' => 'ROLE_ORGANISATEUR',
                ],
                'placeholder' => 'Sélectionner votre role',
                'expanded' => true, // Optional: renders the choices as radio buttons.
                'multiple' => true, // Optional: allows selecting multiple roles.
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}