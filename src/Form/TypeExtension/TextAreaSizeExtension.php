<?php


namespace App\Form\TypeExtension;


use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextAreaSizeExtension implements FormTypeExtensionInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['rows'] = $options['rows']; // Creating a new option/variable so that it can be used on any Form
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([ // Creating a new 'rows' option with a default value of 10. Since it is created in this extensions class, this option can be used and overridden in all other form classes.
            'rows' => 10
        ]);
    }

    public function getExtendedType()
    {
        return TextareaType::class;
    }

}