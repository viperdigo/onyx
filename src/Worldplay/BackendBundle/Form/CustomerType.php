<?php

namespace Worldplay\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('address')
            ->add('zipcode', new ZipcodeType())
            ->add('phone', new PhoneType())
            ->add('email')
            ->add(
                'balance',
                new MoneyType(),
                array(
                    'required' => false,
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Worldplay\CoreBundle\Entity\Customer',
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'customer';
    }
}
