<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
