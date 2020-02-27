<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Model\UserRegistrationFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('plainPassword', PasswordType::class)
            ->add('agreeTerms', CheckboxType::class)
        ;
    }

//                 ALL THE COMMENTED CODE BELOW IS OLD, BUT IT DOES THE SAME AS WE HAVE NOW.
//
//                // Since we still want this field on the form, but don't want it persisted to the database.
//                // We can add the option 'mapped' set to false which means that it will not try to get or set its data back onto the user object
//                // By giving it the PasswordType class, the field acts like a proper password field and does not show what is typed in the field
//
//                  ->add('plainPassword', PasswordType::class, [
//
//                //'mapped' => false,
//
//                'constraints' => [  // Adding a constraints array option to directly add validation to the plainPassword field in the form
//                    new NotBlank([ // To do this, I am creating a new NotBlank object, and passing the options to it in an array.
//                        'message' => 'Choose a password'
//                    ]),
//                    new Length([ // Adding another constraint by creating a new Length object and passing it 'min' and 'minMessage' values
//                                // I am getting 'min' and 'message' etc. variables from the class objects I am creating
//                                // By creating a new Length class, I can pass it its properties, which are where min and minMessage come from
//                                // and set values to them for our form validation.
//                        'min' => 5,
//                        'minMessage' => 'Think of a longer password'
//                    ])
//
//                ]
//            ]
//
//            ->add('agreeTerms', CheckboxType::class, [
//                'constraints' => [ // Adding a constraint and setting it to a new isTrue object because we need this fields value to be true, not false
//                    new isTrue([ // Setting a custom message to the isTrue object.
//                        'message' => 'You must agree to our terms'
//                    ])
//                ]
//            ])
//        ;
//    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserRegistrationFormModel::class,
        ]);
    }
}
