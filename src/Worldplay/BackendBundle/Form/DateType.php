<?php

namespace Worldplay\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateType extends AbstractType
{

    /**
     * @var Boolean
     */
    private $now;

    public function __construct($now = false)
    {
        $this->now = $now;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $arrValue = array();
        if ($this->now) {
            $now = new \DateTime('now');
            $arrValue = array('value' => $now->format('d/m/Y'));
        }

        $resolver->setDefaults(
            array(
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'attr' => array_merge(array('class' => 'date-picker input-medium'), $arrValue),
            )
        );
    }

    public function getParent()
    {
        return 'date';
    }

    public function getName()
    {
        return 'date_type';
    }
}
