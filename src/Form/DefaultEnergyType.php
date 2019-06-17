<?php

namespace One\CheckJeHuis\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultEnergyType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'config_default_energy';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('gas', TextType::class, array('label' => 'Gas'))
            ->add('electricity', TextType::class, array('label' => 'Elektriciteit'))
            ->add('electricHeating', TextType::class, array('label' => 'Electrisch verwarmen'))
            ->add('oil', TextType::class, array('label' => 'Stookolie'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'One\CheckJeHuis\Entity\DefaultEnergy',
            'csrf_protection' => false,
        ));
    }
}
