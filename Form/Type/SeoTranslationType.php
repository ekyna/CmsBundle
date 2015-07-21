<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SeoTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class SeoTranslationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'ekyna_core.field.title',
                'required' => true,
                'admin_helper' => 'SEO_TITLE',
            ))
            ->add('description', 'textarea', array(
                'label' => 'ekyna_core.field.description',
                'required' => true,
                'admin_helper' => 'SEO_DESCRIPTION',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Ekyna\Bundle\CmsBundle\Entity\SeoTranslation',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_seo_translation';
    }
}
