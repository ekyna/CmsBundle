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
                'attr'      => array(
                    'widget_col' => 12,
                ),
            ));

        if ($options['advanced']) {
            $builder
                ->add('changefreq', 'choice', array(
                    'label'        => 'ekyna_core.field.changefreq',
                    'admin_helper' => 'CMS_SEO_CHANGEFREQ',
                    'choices'      => array(
                        'hourly'  => 'ekyna_core.changefreq.hourly',
                        'monthly' => 'ekyna_core.changefreq.monthly',
                        'yearly'  => 'ekyna_core.changefreq.yearly',
                    ),
                    'required'     => true,
                ))
                ->add('priority', 'number', array(
                    'label'        => 'ekyna_core.field.priority',
                    'admin_helper' => 'CMS_SEO_PRIORITY',
                    'precision'    => 1,
                    'required'     => true,
                ))
                ->add('follow', 'checkbox', array(
                    'label'        => 'ekyna_core.field.follow',
                    'admin_helper' => 'CMS_SEO_FOLLOW',
                    'required'     => false,
                    'attr'         => array('align_with_widget' => true),
                ))
                ->add('index', 'checkbox', array(
                    'label'        => 'ekyna_core.field.index',
                    'admin_helper' => 'CMS_SEO_INDEX',
                    'required'     => false,
                    'attr'         => array('align_with_widget' => true),
                ))
                ->add('canonical', 'url', array(
                    'admin_helper' => 'CMS_SEO_CANONICAL',
                    'label'        => 'ekyna_core.field.canonical_url',
                    'required'     => false,
                ));
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
                'label'             => false,
                'attr'              => array('widget_col' => 12),
                'advanced'          => true,
                'validation_groups' => array($this->dataClass),
            ))
            ->setAllowedTypes(array(
                'advanced' => 'bool',
            ));

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_seo';
    }
}
