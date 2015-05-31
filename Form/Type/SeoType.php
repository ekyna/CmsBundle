<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SeoType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class SeoType extends ResourceFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translationsForms', array(
                'form_type' => new SeoTranslationType(),
                'label'     => false,
                'attr' => array(
                    'widget_col' => 12,
                ),
            ))
        ;

        if ($options['advanced']) {
            $builder
                ->add('changefreq', 'choice', array(
                    'label' => 'ekyna_core.field.changefreq',
                    'choices' => array(
                        'hourly' => 'ekyna_core.changefreq.hourly',
                        'monthly' => 'ekyna_core.changefreq.monthly',
                        'yearly' => 'ekyna_core.changefreq.yearly',
                    ),
                    'required' => true,
                    'admin_helper' => 'SEO_CHANGEFREQ',
                ))
                ->add('priority', 'number', array(
                    'label' => 'ekyna_core.field.priority',
                    'precision' => 1,
                    'required' => true,
                    'admin_helper' => 'SEO_PRIORITY',
                ))
                ->add('follow', 'checkbox', array(
                    'label' => 'ekyna_core.field.follow',
                    'required' => false,
                    'attr' => array('align_with_widget' => true),
                    'admin_helper' => 'SEO_FOLLOW',
                ))
                ->add('index', 'checkbox', array(
                    'label' => 'ekyna_core.field.index',
                    'required' => false,
                    'attr' => array('align_with_widget' => true),
                    'admin_helper' => 'SEO_INDEX',
                ))
                ->add('canonical', 'url', array(
                    'label' => 'ekyna_core.field.canonical_url',
                    'required' => false,
                    'admin_helper' => 'SEO_CANONICAL',
                ))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['advanced'] = $options['advanced'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setDefaults(array(
                'label' => false,
                'attr' => array('widget_col' => 12),
                'advanced' => true,
                'validation_groups' => array($this->dataClass),
            ))
            ->setAllowedTypes(array(
                'advanced' => 'bool',
            ))
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
    	return 'ekyna_cms_seo';
    }
}
