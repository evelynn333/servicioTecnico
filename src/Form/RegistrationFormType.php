<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'El correo es obligatorio',
                    ]),
                ],
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Correo...',
                )
            ])
            ->add('Password', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La contraseña es obligatoria',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'La contraseña debe tener un mínimo de {{ limit }} carácteres.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Contraseña...',
                )
            ])
            ->add("nombre", TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Nombre...',
                    ]),
                ],
                'label' => false,
               'attr' => array(
                    'placeholder' => 'El nombre es obligatorio',
                    
                )
            ])
            ->add("apellidos", TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Nombre...',
                    ]),
                ],
               'label' => false,
               'attr' => array(
                    'placeholder' => 'Apellidos...',
                   
                )
            ])
            ->add("telefono", TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'El telefono es obligatorio',
                    ]),
                ],
               'label' => false,
               'attr' => array(
                    'placeholder' => 'Telefono...',
                )
            ])
            ->add("foto", FileType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'La foto es obligatoria',
                    ]),
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Elige un formato válido.',
                    ])
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Acepta los términos y condiciones.',
                    ]),
                ],
                'attr' => array(
                    'class' => 'form-check-input'
                ),
                'label' => "Terminos y Condiciones ",
            ])
               ->add('Registrar', SubmitType::class,);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
