<?php

namespace Worldplay\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('supplier')
            ->add('description')
            ->add('costValue', new MoneyType())
            ->add('saleValue', new MoneyType())
            ->add('storage')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Worldplay\CoreBundle\Entity\Product'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'product';
    }
}
