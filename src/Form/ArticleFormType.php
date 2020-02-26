<?php


namespace App\Form;


use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleFormType extends AbstractType
{

    private $userRepository;

    public function __construct(UserRepository $userRepository) // Using dependency injection to allow us to use the UserRepository class methods by creating an object of it.
    {

        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) // Overriding the buildForm function in the AbstractType to build our form
    {
        $builder // Using the builder object to add two fields to our form. These are the two most important properties inside the Article entity class
            // These add methods have 3 arguments, the field name, field type and some options that come with the type chosen
            ->add('title', TextType::class, [ // Passing a type argument to make this field a Text field and an options array
                'help' => 'Choose something'// This is the options array. Since I am using the text type, I can use options that come with that type, in this case I am using the option 'help'
                                                // And setting the value of it to a helpful message that will show near the title text box
        ])
            ->add('content') // When I add this field, the form system calls getContent to read data off of the article object and when we submit it calls setContent to set the data back on the article object
            ->add('publishedAt', null, [ // Setting the type to null so that Symfony keeps trying to guess the field type
                'widget' => 'single_text'
            ])
            ->add('author', EntityType::class, [ // Since we have added our own field type and this field type requires a class so that it can query it, we have to pass it the class we want it to query
                'class' => User::class, // We want it to query the User class because we want it to display the names of all the Users so that the admin can pick one of them to become an author of a new article
                'choice_label' => function(User $user) { // Passing the option a callback which Symfony will call for each item and pass it the data for that option, a user object in this case
                    return sprintf('(%d) %s', $user->getId(), $user->getEmail()); // Inside the callback I am returning the User class methods getId() and getEmail() to get that properties data and assign it to d and s in the format
                },
                'placeholder' => 'Choose an author', // Setting the placeholder option to a message.
                'choices' => $this->userRepository // Overriding the 'choices' option (which is what show up in the drop down menu) to a database query that finds all the users by their emails in alphabetical order.
                    ->findAllEmailAlphabetical(),
                'invalid_message' => 'Symfony is too smart for your hacking' // Setting the sanity validation error message to a custom message
            ])

        ;

    }

    public function configureOptions(OptionsResolver $resolver) // This is where you can set options that control how your form behaves
    {
        $resolver->setDefaults([ // Using the OptionsResolver object to call the function setDefaults to set some options for my form
            'data_class' => Article::class // The most important option is this one. This binds this form to that class
        ]);
    }


}