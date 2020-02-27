<?php


namespace App\Form\DataTransformer;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EmailToUserTransformer implements DataTransformerInterface
{

    private $userRepository;

    private $finderCallback;

    public function __construct(UserRepository $userRepository, callable $finderCallback) // Since the reverseTransform method needs to query the database for the User, we need to use dependency injection to give this class access to the UserRepository
                                                                // However, since this class is not a service, we will need to pass it manually to this class in the UserSelectTextType addModelTransformer method
    {
        $this->userRepository = $userRepository;
        $this->finderCallback = $finderCallback;
    }

    public function transform($value) // This method is called when the form is rendering, it takes the raw data for a field, in this case a User object that lives on the $author property
                                        // and its job is to transform that into a representation that can be used for the form field. In other words, the email string
    {
        if (null === $value) { // if $value is exactly equal to null
            return ''; // then return an empty string
        }

        if (!$value instanceof User) { // if $value is not an instanceof User. i.e. an object of User
            throw new \LogicException('The UserSelectTextType can only be used with User objects');
        }

        return $value->getEmail();  // if $value is an instance of a User, then get their email
    }

    public function reverseTransform($value) // Gets the email string from the method above and uses it to query for a User object and return it
    {

        if (!$value) { // if the field on the form is empty and submitted, then null should be passed ot setAuthor()
            return;
        }

        $callback = $this->finderCallback; // Who ever creates a new transformer object will pass in a $callback that is responsible for querying the database for the user
        $user = $callback($this->userRepository, $value);

        if (!$user) { // if there is not a user with that email
            throw new TransformationFailedException(sprintf( // Throw a new transformation failed exception with a message that uses the email string ($value). T
                                                            // This exception is special because it comes up as a validation error. This is an example of Sanity validation, which is where validation is built into the form field itself
                'No user found with email "%s"', $value
            ));
        }

        return $user; // if there is a user with that email, then simply return it
    }

}