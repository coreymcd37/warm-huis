<?php

namespace One\CheckJeHuis\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HouseEmailType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'house_email';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('email', EmailType::class, array(
                'required' => true
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'One\CheckJeHuis\Entity\House',
        ));
    }
}
