<?php

namespace One\CheckJeHuis\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Trsteel\CkeditorBundle\Form\Type\CkeditorType;

class ContentType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'content';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if (array_key_exists('allow_deactivation', $options) && $options['allow_deactivation'] === true) {
            $builder->add('active', CheckboxType::class, array('label' => 'Actief', 'required' => false));
        }

        $builder
            ->add('value', CkeditorType::class, array(
                'toolbar' => array('basicstyles', 'links', 'paragraph', 'styles', 'insert'),
                'toolbar_groups' => array('insert'=> array('Image')),
                'height' => 400,
                'transformers' => [],
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'One\CheckJeHuis\Entity\Content',
            'csrf_protection' => false,
            'allow_deactivation' => false,
        ));
    }
}
