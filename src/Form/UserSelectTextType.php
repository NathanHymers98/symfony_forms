<?php


namespace App\Form;


use App\Entity\User;
use App\Form\DataTransformer\EmailToUserTransformer;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class UserSelectTextType extends AbstractType // Creating a custom field type
{

    private $userRepository;

    private $router;

    public function __construct(UserRepository $userRepository, RouterInterface $router) // Since form type classes are services, autowiring will work here.
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new EmailToUserTransformer(
            $this->userRepository, // Passing the user repository manually since the EmailToUserTransformer class cannot access it through dependency injection alone because it is not a service class
            $options['finder_callback']
        ));
    }


    public function getParent() // Overriding the getParent method
    {
        return TextType::class; // returning the TextType class.
                                // By saying that the TexType class is the parent, we're saying that unless we say otherwise, we want this field to look and behave like a normal text type field

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'User not found', // Setting the default error message for a validation error with this field type to the following
            // Creating a brand new field option which allows the callback to be customised each time we use this field
            'finder_callback' => function(UserRepository $userRepository, string $email) { // The default value of this custom option is a callback function that accepts a UserRepository argument and the value
                return $userRepository->findOneBy(['email' => $email]); // Inside I am returning the normal user email from the database
            },
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options) // This method is where we can creates variables and change existing ones
    {
        $attr = $view->vars['attr']; // the view object has a public vars array property that holds all of the things that will eventually become the variables
        $class = isset($attr['class']) ? $attr['class']. '' : ''; // Grabbing the class. if the class is set on attr, use it. But add a space on the end. If there is no class yet set this to be blank
        $class .= 'js-user-autocomplete'; // Always appending js-user-autocomplete

        $attr['class'] = $class; // Setting the new class string back on
        $attr['data-autocomplete-url'] = $this->router->generate('admin_utility_users'); // In order to generate the URL so that we can send it to the autocompleter,
                                                                                                // we needed to use the RouterInterface object to give us access to the generate() method and pass it the URL of the endpoint that prints out the users JSON data
                                                                                                // so that the autocompleter can use this data and use it to help with autocomplition of users emails

        $view->vars['attr'] = $attr; // Setting all of the code above back to the view object
    }


}