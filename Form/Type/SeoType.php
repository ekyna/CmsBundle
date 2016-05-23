<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Ekyna\Bundle\CmsBundle\Model\ChangeFrequencies;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('translations', TranslationsFormsType::class, array(
                'form_type' => SeoTranslationType::class,
                'label'     => false,
                'attr'      => array(
                    'widget_col' => 12,
                ),
            ))
        ;

        if ($options['advanced']) {
            $builder
                ->add('changefreq', Type\ChoiceType::class, array(
                    'label'        => 'ekyna_core.field.changefreq',
                    'admin_helper' => 'CMS_SEO_CHANGEFREQ',
                    'choices'      => ChangeFrequencies::getChoices(),
                    'required'     => true,
                ))
                ->add('priority', Type\NumberType::class, array(
                    'label'        => 'ekyna_core.field.priority',
                    'admin_helper' => 'CMS_SEO_PRIORITY',
                    'scale'        => 1,
                    'required'     => true,
                ))
                ->add('follow', Type\CheckboxType::class, array(
                    'label'        => 'ekyna_core.field.follow',
                    'admin_helper' => 'CMS_SEO_FOLLOW',
                    'required'     => false,
                    'attr'         => array('align_with_widget' => true),
                ))
                ->add('index', Type\CheckboxType::class, array(
                    'label'        => 'ekyna_core.field.index',
                    'admin_helper' => 'CMS_SEO_INDEX',
                    'required'     => false,
                    'attr'         => array('align_with_widget' => true),
                ))
                ->add('canonical', Type\UrlType::class, array(
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
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults(array(
                'label'             => false,
                'attr'              => array('widget_col' => 12),
                'advanced'          => true,
                'validation_groups' => array($this->dataClass),
            ))
            ->setAllowedTypes('advanced', 'bool');

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_seo';
    }
}
