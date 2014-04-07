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
                'label' => 'Titre',
                'required' => true
            ))
            ->add('description', 'textarea', array(
                'label' => 'Description',
                'required' => true
            ))
            ->add('changefreq', 'choice', array(
                'label' => 'Fréqu. de modif.',
                'choices'   => array(
                    'hourly' => 'Toutes les heures', 
                    'monthly' => 'Tous les mois',
                    'yearly' => 'Tous les ans',
                ),
                'required' => true
            ))
            ->add('priority', 'number', array(
                'label' => 'Priorité',
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
