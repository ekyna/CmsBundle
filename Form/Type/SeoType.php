<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * SeoType
 */
class SeoType extends AbstractType
{
    protected $dataClass;

    public function __construct($class)
    {
        $this->dataClass = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'ekyna_core.field.title',
                'required' => true
            ))
            ->add('description', 'textarea', array(
                'label' => 'ekyna_core.field.description',
                'required' => true
            ))
            ->add('changefreq', 'choice', array(
                'label' => 'ekyna_core.field.changefreq',
                'choices'   => array(
                    'hourly' => 'ekyna_core.changefreq.hourly', 
                    'monthly' => 'ekyna_core.changefreq.monthly',
                    'yearly' => 'ekyna_core.changefreq.yearly',
                ),
                'required' => true
            ))
            ->add('priority', 'number', array(
                'label' => 'ekyna_core.field.priority',
                'precision' => 1,
                'required' => true
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
        ));
    }

    public function getName()
    {
    	return 'ekyna_cms_seo';
    }
}
