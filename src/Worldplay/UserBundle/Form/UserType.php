<?php

namespace Worldplay\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('name')
            ->add(
                'plainPassword',
                'repeated',
                array(
                    'options'=>array(
                        'attr' => array(
                            'autocomplete' => 'off'
                        ),
                    ),
                    'invalid_message' => 'As senhas não correspondem.',
                    'first_name' => 'Senha',
                    'second_name' => 'Confirma_senha',
                    'type' => 'password',
                )
            )
            ->add('isActive')
            ->add('userType')
            ->add('email');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Worldplay\CoreBundle\Entity\User'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }
}
