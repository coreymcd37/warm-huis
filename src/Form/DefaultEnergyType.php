<?php

namespace One\CheckJeHuis\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DefaultEnergyType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'config_default_energy';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('gas', 'text', array('label' => 'Gas'))
            ->add('electricity', 'text', array('label' => 'Elektriciteit'))
            ->add('electricHeating', 'text', array('label' => 'Electrisch verwarmen'))
            ->add('oil', 'text', array('label' => 'Stookolie'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'One\CheckJeHuis\Entity\DefaultEnergy',
            'csrf_protection' => false,
        ));
    }
}
