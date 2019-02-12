<?php

namespace One\CheckJeHuis\Form;

use One\CheckJeHuis\Service\HouseService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentType extends AbstractType
{
    protected $allowDeactivation;

    public function __construct($allowDeactivation = true)
    {
        $this->allowDeactivation = $allowDeactivation;
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'content';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if ($this->allowDeactivation) {
            $builder->add('active', 'checkbox', array('label' => 'Actief', 'required' => false));
        }

        $builder
            ->add('value', 'ckeditor', array(
                'toolbar' => array('basicstyles', 'links', 'paragraph', 'styles', 'insert'),
                'toolbar_groups' => array('insert'=> array('Image')),
                'height' => 400,
                'transformers' => [],
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'One\CheckJeHuis\Entity\Content',
            'csrf_protection' => false,
        ));
    }
}
