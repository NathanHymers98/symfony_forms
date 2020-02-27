<?php


namespace App\Form\Model;

use App\Validator\UniqueUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


class UserRegistrationFormModel // The purpose of this class is to hold data about the user registration form, so it doesn't need to extend anything
{

    // I have added the my custom annotation @UniqueUser() above the email property because we only want to validate that the email that is being passed
    // is not already stored in the database and linker to another user.
    /**
     * @Assert\NotBlank(message="Please enter an email")
     * @Assert\Email()
     * @UniqueUser()
     */
    // Creating the user registration form fields as public properties
    public $email;

    /**
     * @Assert\NotBlank(message="Chhose a password")
     * @Assert\Length(min=5, minMessage="Think of a longer password")
     */
    public $plainPassword;

    /**
     * @Assert\IsTrue(message="You must agree to our terms")
     */
    public $agreeTerms;
}