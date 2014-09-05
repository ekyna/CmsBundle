<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * SeoType
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class SeoType extends ResourceFormType
{
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

    public function getName()
    {
    	return 'ekyna_cms_seo';
    }
}
