<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            // don't use password, avoid EVER setting that on a
            // field that might be persisted
            ->add('plainPassword', PasswordType::class, [ // Since we still want this field on the form, but don't want it persisted to the database.
                                                                    // We can add the option 'mapped' set to false which means that it will not try to get or set its data back onto the user object
                                                                    // By giving it the PasswordType class, the field acts like a proper password field and does not show what is typed in the field
                'mapped' => false,
                'constraints' => [  // Adding a constraints array option to directly add validation to the plainPassword field in the form
                    new NotBlank([ // To do this, I am creating a new NotBlank object, and passing the options to it in an array.
                        'message' => 'Choose a password'
                    ]),
                    new Length([ // Adding another constraint by creating a new Length object and passing it 'min' and 'minMessage' values
                                // I am getting 'min' and 'message' etc. variables from the class objects I am creating
                                // By creating a new Length class, I can pass it its properties, which are where min and minMessage come from
                                // and set values to them for our form validation.
                        'min' => 5,
                        'minMessage' => 'Think of a longer password'
                    ])

                ]
            ])
            ->add('agreeTerms', CheckboxType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
