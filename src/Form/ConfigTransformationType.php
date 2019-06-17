<?php

namespace One\CheckJeHuis\Form;

use One\CheckJeHuis\Entity\Config;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigTransformationType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'config_transformation';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('value', TextType::class, array('label' => 'Waarde'))
            ->add('unit', ChoiceType::class, array('label' => 'Eenheid', 'choices' => Config::getUnits()))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'One\CheckJeHuis\Entity\ConfigTransformation',
            'csrf_protection' => false,
        ));
    }
}
