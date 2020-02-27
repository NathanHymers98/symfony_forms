<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUserValidator extends ConstraintValidator // The actual validation for our custom annotation is done here
{

    private $userRepository;

    public function __construct(UserRepository $userRepository) // Since this is a service class
                                                                // I can easily use dependency injection to get access
                                                                // to the UserRepository class object so that we can query
                                                                // the user database table
    {
        $this->userRepository = $userRepository;
    }

    public function validate($value, Constraint $constraint) // The constraint object will be the UniqueUser object that does the annotation
    {
        /* @var $constraint \App\Validator\UniqueUser */

        $existingUser = $this->userRepository->findOneBy([
            'email' => $value // Since we put the @UniqueUser annotation above the email property class, $value will be that properties value
        ]);

        if (!$existingUser) { // if not an existing user, then return. If it is an existing user, then build the violation message.
            return;
        }

        $this->context->buildViolation($constraint->message) // When a form is submitted and the email they have enter is already linked
                                                            // to a user, then this method will be called, which gets the $message property from
                                                            // the $constraint object (UniqueUser object) and displays it
            ->addViolation();
    }
}
