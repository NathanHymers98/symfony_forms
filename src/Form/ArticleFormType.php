<?php


namespace App\Form;


use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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

        /** @var  Article|null $article */
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
            ->add('location', ChoiceType::class, [ // making a new location field and setting it as a choicetype class so it is a dropdown list
                'placeholder' => 'Choose a location',
                'choices' => [ // Setting the choices of this drop down to the following
                    'The Solar System' => 'solar_system', // The 'key' e.g. 'The Solar System' is what is displayed in the dropdown and the 'value' e.g. 'solar_system' is what is set onto our entity if this option is selected so it is ultimately the string that will be saved to the database
                    'Near a star' => 'star',
                    'Interstellar Space' => 'interstellar_space'
                ],
                'required' => false, // Setting required to false, because the location property in the entity class Article is set to nullable, meaning that it is optional in the database
            ])

        ;

        if ($options['include_published_at']) { // if the option 'include_published_at' exists
            $builder->add('publishedAt', null, [ // Then add it to the form
                'widget' => 'single_text'
            ]);
        }

        $builder->get('location')->addEventListener( // Getting the FormBuilder object for the field 'location' and adding an event listener to only that field, not the entire field
            FormEvents::POST_SUBMIT, // This FormEvents class holds a constant for each event we can hook into for the form system. POST_SUBMIT is one of these constants
            function(FormEvent $event) { // Passing a callback as the second argument, Symfony will pass that a FormEvent object
                $form = $event->getForm();
                $this->setupSpecificLocationNameField(
                    $form->getParent(),
                    $form->getData()
                );
            }
        );


        $builder->addEventListener( // Attaching the event listener to the entire form, which means our callback will be passed info about the entire form, which is usually what you want
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) {
                /** var Article|null $data */
                $data = $event->getData();
                if (!$data) {
                    return;
                }

                $this->setupSpecificLocationNameField(
                    $event->getForm(),
                    $data->getLocation()
                );
            }
        );


    }

    public function configureOptions(OptionsResolver $resolver) // This is where you can set options that control how your form behaves
    {
        $resolver->setDefaults([ // Using the OptionsResolver object to call the function setDefaults to set some  default options for my form that can be overridden
            'data_class' => Article::class, // The most important option is this one. This binds this form to that class
            'include_published_at' => false // Creating a new option for the Article forms to enable and disable the published_at field. By default it is set to false
        ]);
    }

    private function getLocationNameChoices(string $location) // We pass this one of the location strings we defined in the field
                                                                // And it returns the choices for the specificLocationName field
    {
        $planets = [ // If 'the_solar_system' string is passed to this function it will return this list of planets
            'Mercury',
            'Venus',
            'Earth',
            'Mars',
            'Jupiter',
            'Saturn',
            'Uranus',
            'Neptune',
        ];
        $stars = [ // if 'star' string is passed to this function it will return this list of stars
            'Polaris',
            'Sirius',
            'Alpha Centauari A',
            'Alpha Centauari B',
            'Betelgeuse',
            'Rigel',
            'Other'
        ];
        $locationNameChoices = [
            'solar_system' => array_combine($planets, $planets), // Using array combine just because I want the display values and the values set back on my entity properties to be the same.
                                                                    // This is the same as writing 'Mercury" => 'Mercury' instead of just 'Mercury'
            'star' => array_combine($stars, $stars),
            'interstellar_space' => null, // Unlike the other field options, when 'interstellar_space' string is passsed to this function, we don't want the second drop down to display, so it it gets set to null
        ];
        return $locationNameChoices[$location] ?? null; // The ?? null part says that if the location key is set, use it. Else use null
    }

    private function setupSpecificLocationNameField(FormInterface $form, ?String $location) // The job of this function will be to dynamically add the specific location name field with the correct choices
                                                                                            // It accepts a FormInterface object because
                                                                                            // The string has question mark so that the $location variable can be null
    {
        // The idea of this if statement is: If when I originally render the form, there was a location set, then thanks to our logic above where we add and remove the specificLocationName field, there will be this field on the form
        // But if we changed it to Choose a location, meaning we are not setting a location then we want to remove the specificLocationName field before we do any validation
        if (null === $location) { // if the location is exactly equal to null
            $form->remove('specificLocationName'); // then remove the specificLoactionName field from the form and return

            return;
        }

        $choices = $this->getLocationNameChoices($location);

        // This if statement below is needed when the user selects the interstellar space location on the form
        if (null === $choices) {
            $form->remove('specificLocationName');

            return;
        }

        $form->add('specificLocationName', ChoiceType::class, [
            'placeholder' => 'Where exactly?',
            'choices' => $choices,
            'required' => false,
        ]);
    }


}