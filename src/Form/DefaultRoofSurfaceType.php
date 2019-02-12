<?php

namespace One\CheckJeHuis\Form;

use One\CheckJeHuis\Service\HouseService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DefaultRoofSurfaceType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'config_default_roof';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('surface', 'text', array('label' => 'Oppervlak'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'One\CheckJeHuis\Entity\DefaultRoof',
            'csrf_protection' => false,
        ));
    }
}
