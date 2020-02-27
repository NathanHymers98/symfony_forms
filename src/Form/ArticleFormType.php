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

        $article = $options['data'] ?? null; // This code says: I want the article variable to be equal to the option 'data' if it exists and is not null, but if it does not exist, then set it to null.
        $isEdit = $article && $article->getId(); // Checking that the $article variable is an object and that it also has an id.

        $builder // Using the builder object to add two fields to our form. These are the two most important properties inside the Article entity class
            // These add methods have 3 arguments, the field name, field type and some options that come with the type chosen

            ->add('title', TextType::class, [ // Passing a type argument to make this field a Text field and an options array
                'help' => 'Choose something'// This is the options array. Since I am using the text type, I can use options that come with that type, in this case I am using the option 'help'
                                                // And setting the value of it to a helpful message that will show near the title text box
        ])
            ->add('content', null, [ // When I add this field, the form system calls getContent to read data off of the article object and when we submit it calls setContent to set the data back on the article object
                'rows' => 15
            ])
            ->add('author', UserSelectTextType::class, [ // Using our own field type class that makes the author field behave like a normal text type field
                'disabled' => $isEdit // disabling this field on the edit page so the author of a article cannot be changed
            ])

        ;

        if ($options['include_published_at']) { // if the option 'include_published_at' exists
            $builder->add('publishedAt', null, [ // Then add it to the form
                'widget' => 'single_text'
            ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver) // This is where you can set options that control how your form behaves
    {
        $resolver->setDefaults([ // Using the OptionsResolver object to call the function setDefaults to set some options for my form
            'data_class' => Article::class, // The most important option is this one. This binds this form to that class
            'include_published_at' => false // Creating a new option for the Article forms to enable and disable the published at field
        ]);
    }


}