<?php

namespace App\Validator;

use Doctrine\Common\Annotations\Annotation\Target;
use Symfony\Component\Validator\Constraint;


// The @Target annotation tells Symfony that this custom annotation is ok for it to be used for a property, method or even inside another annotation
/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class UniqueUser extends Constraint // This is the class that we will use to create our own annotaion for UserRegistrationFormModel so that an object of that class can be unique
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'I think you are already registered'; // This is the annotation message that will appear when someone tries to create an account with an email that is already in the database
}
