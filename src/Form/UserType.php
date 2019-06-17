<?php

namespace One\CheckJeHuis\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Naam',
            ])
            ->add('city', EntityType::class, [
                'class' => 'One\CheckJeHuis\Entity\City',
                'choice_label' => 'name',
                'label' => 'Organisatie',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rollen',
                'choices' => [
                     'Admin' => 'ROLE_ADMIN',
                     'Organisatie beheerder' => 'ROLE_CITY',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Actief',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'One\CheckJeHuis\Entity\User',
        ));
    }
}
