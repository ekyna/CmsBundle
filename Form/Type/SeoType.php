<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
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
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new SeoTranslationType(),
                'label'     => false,
                'attr' => [
                    'widget_col' => 12,
                ],
            ])
        ;

        if ($options['advanced']) {
            $builder
                ->add('changefreq', 'choice', [
                    'label' => 'ekyna_core.field.changefreq',
                    'choices' => [
                        'hourly' => 'ekyna_core.changefreq.hourly',
                        'monthly' => 'ekyna_core.changefreq.monthly',
                        'yearly' => 'ekyna_core.changefreq.yearly',
                    ],
                    'required' => true,
                    'admin_helper' => 'SEO_CHANGEFREQ',
                ])
                ->add('priority', 'number', [
                    'label' => 'ekyna_core.field.priority',
                    'precision' => 1,
                    'required' => true,
                    'admin_helper' => 'SEO_PRIORITY',
                ])
                ->add('follow', 'checkbox', [
                    'label' => 'ekyna_core.field.follow',
                    'required' => false,
                    'attr' => ['align_with_widget' => true],
                    'admin_helper' => 'SEO_FOLLOW',
                ])
                ->add('index', 'checkbox', [
                    'label' => 'ekyna_core.field.index',
                    'required' => false,
                    'attr' => ['align_with_widget' => true],
                    'admin_helper' => 'SEO_INDEX',
                ])
                ->add('canonical', 'url', [
                    'label' => 'ekyna_core.field.canonical_url',
                    'required' => false,
                    'admin_helper' => 'SEO_CANONICAL',
                ])
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
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'label' => false,
                'attr' => ['widget_col' => 12],
                'advanced' => true,
                'validation_groups' => [$this->dataClass],
            ])
            ->setAllowedTypes('advanced', 'bool')
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
