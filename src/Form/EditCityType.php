<?php

namespace One\CheckJeHuis\Form;

use One\CheckJeHuis\Command\EditCityCommand;
use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Entity\House;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class EditCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Naam',
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'constraints' => new Email(),
            ])
            ->add('url', TextType::class, [
                'label' => 'Url',
            ])
            ->add('cityName', TextType::class, [
                'label' => 'Gemeente',
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Postcode',
            ])
            ->add('prefillCity', CheckboxType::class, [
                'label' => 'Prefill postcode & gemeente',
                'required' => false,
            ])
            ->add('nisNumber', TextType::class, [
                'label' => 'NIS-code',
            ])
            ->add('deleteHeaderLogoImage', CheckboxType::class, [
                'label' => 'Verwijder header logo',
                'required' => false,
            ])
            ->add('headerLogoImage', FileType::class, [
                'label' => 'Logo header afbeelding',
                'image_property' => 'getHeaderLogoImagePath',
                'required' => false,
            ])
            ->add('deleteFooterLogoImage', CheckboxType::class, [
                'label' => 'Verwijder footer logo',
                'required' => false,
            ])
            ->add('footerLogoImage', FileType::class, [
                'label' => 'Logo footer afbeelding',
                'image_property' => 'getFooterLogoImagePath',
                'required' => false,
            ])
            ->add('footerLogoLink', TextType::class, [
                'label' => 'Logo footer link',
            ])
            ->add('backgroundColor', TextType::class, [
                'label' => 'Achtergrondkleur',
                'attr' => [
                    'class' => 'jscolor',
                ]
            ])
            ->add('backgroundColorHeader', TextType::class, [
                'label' => 'Achtergrondkleur header & footer',
                'attr' => [
                    'class' => 'jscolor',
                ]
            ])
            ->add('buttonColor', TextType::class, [
                'label' => 'Kleur knoppen',
                'attr' => [
                    'class' => 'jscolor',
                ]
            ])
            ->add('textColor', TextType::class, [
                'label' => 'Kleur tekst',
                'attr' => [
                    'class' => 'jscolor',
                ]
            ])
            ->add('textColorHeader', TextType::class, [
                'label' => 'Kleur tekst header & footer',
                'attr' => [
                    'class' => 'jscolor',
                ]
            ])
            ->add('javascriptBottom', TextareaType::class, [
                'label' => 'Google analytics',
                'required' => false,
                'attr' => array('rows' => '10'),
            ])
            ->add('sunPotentialMapLink', TextType::class, [
                'label' => 'Link naar zonnepotentieel kaart',
                'required' => false,
            ])
            ->add('thermographicPhotoLink', TextType::class, [
                'label' => 'Link naar thermografische luchtfoto',
                'required' => false,
            ])
            ->add('deleteThermographicPhotoImage', CheckboxType::class, [
                'label' => 'Verwijder thermografische luchtfoto',
                'required' => false,
            ])
            ->add('thermographicPhotoImage', FileType::class, [
                'label' => 'Afbeelding thermografische luchtfoto',
                'image_property' => 'getThermographicPhotoImagePath',
                'required' => false,
            ])
            ->add('showInDropdown', CheckboxType::class, [
                'required' => false,
                'label' => 'Toon omgeving op startpagina',
            ])
            ->add('showSpecificInfo', CheckboxType::class, [
                'required' => false,
                'label' => 'Toon popup met extra info bij start',
            ])
            ->add('stayUpToDate', ChoiceType::class, [
                'label' => "Checkbox 'Ik blijf graag op de hoogte'",
                'expanded' => true,
                'choices'  => [
                    'Toon checkbox, niet aangevinkt' => City::STAY_UP_TO_DATE_NOT_CHECKED,
                    'Toon checkbox, aangevinkt' => City::STAY_UP_TO_DATE_CHECKED,
                    'Checkbox niet tonen' => City::STAY_UP_TO_DATE_HIDE,
                ],
            ])
            ->add('defaultBuildingType', ChoiceType::class, [
                'label' => 'Standaard type gebouw',
                'choices' => array_flip(House::getBuildingTypes()),
            ])
            ->add('defaultBuildYear', ChoiceType::class, [
                'label' => 'Standaard bouwjaar',
                'choices' => array_flip(House::getYears()),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => EditCityCommand::class,
        ));
    }
}
