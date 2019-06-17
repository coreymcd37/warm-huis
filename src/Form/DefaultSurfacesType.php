<?php

namespace One\CheckJeHuis\Form;

use One\CheckJeHuis\Service\HouseService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultSurfacesType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'config_default_surfaces';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('livingArea', TextType::class, array('label' => 'Bewoonbaar'))
            ->add('floor', TextType::class, array('label' => 'Grond'))
            ->add('facade', TextType::class, array('label' => 'Gevel'))
            ->add('window', TextType::class, array('label' => 'Ramen'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'One\CheckJeHuis\Entity\DefaultSurface',
            'csrf_protection' => false,
        ));
    }
}
