<?php

namespace One\CheckJeHuis\Form;

use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Entity\House;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MailPlanType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'mail_plan';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $house = $event->getData();
            $form = $event->getForm();
            if ($house instanceof House) {
                $city = $house->getCity();
                $form->add('email', 'email', array(
                    'label' => 'Of mail mijn persoonlijk stappenplan naar',
                    'required' => true,
                ));
                $form->add('address', TextType::class, [
                    'label' => 'Mijn adres',
                    'required' => false,
                    'data' => $house->getAddress() === House::ADDRESS_ANONYMOUS?'':$house->getAddress(),
                ]);

                if ($city->getStayUpToDate() != City::STAY_UP_TO_DATE_HIDE) {
                    $form->add('newsletter', 'checkbox', array(
                        'data' => $city->getStayUpToDate() == City::STAY_UP_TO_DATE_CHECKED ? true : false,
                        'required' => false,
                        'label' => 'Ik blijf graag op de hoogte van nieuwe initiatieven i.v.m. energiezuinig renoveren met premies, bouwadvies en renovatiebegeleiding zowel algemeen als afgestemd op mijn woonsituatie. Neem daarom mijn e-mailadres en/of adres samen met de gegevens over mijn woning op in de databank.',
                    ));
                }
                $cityPostalCode = $city->getPrefillCity()?$city->getPostalCode():'';
                $form->add('postalCode', TextType::class, [
                    'label' => '',
                    'required' => false,
                    'data' => $house->getPostalCode()?:$cityPostalCode,
                ]);

                $cityPrefillName = $city->getPrefillCity()?$city->getCityName():'';
                $form->add('cityName', TextType::class, [
                    'label' => '',
                    'required' => false,
                    'data' => $house->getCityName()?:$cityPrefillName,
                ]);
            }
        });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'One\CheckJeHuis\Entity\House',
            'csrf_protection' => true,
            'validation_groups' => function (FormInterface $form) {
                return ['Default'];
            },
        ));
    }
}
