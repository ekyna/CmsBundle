<?php

namespace Ekyna\Bundle\CmsBundle\Settings;

use Ekyna\Bundle\SettingBundle\Schema\AbstractSchema;
use Ekyna\Bundle\SettingBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Class SeoSettingsSchema
 * @package Ekyna\Bundle\CmsBundle\Settings
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class SeoSettingsSchema extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array_merge(array(
                'title'       => 'Page default title',
                'description' => 'Page default description',
                'locale'      => 'fr',
            ), $this->defaults))
            ->setAllowedTypes(array(
                'title'       => array('string'),
                'description' => array('string'),
                'locale'      => array('string'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label'       => 'ekyna_core.field.title',
                'constraints' => array(
                    new Constraints\NotBlank()
                )
            ))
            ->add('description', 'textarea', array(
                'label'       => 'ekyna_core.field.description',
                'constraints' => array(
                    new Constraints\NotBlank()
                )
            ))
            ->add('locale', 'locale', array(
                'label'       => 'ekyna_core.field.locale',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Locale(),
                )
            ))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'ekyna_core.field.seo';
    }

    /**
     * {@inheritDoc}
     */
    public function getShowTemplate()
    {
        return 'EkynaCmsBundle:Admin/Settings/Seo:show.html.twig';
    }

    /**
     * {@inheritDoc}
     */
    public function getFormTemplate()
    {
        return 'EkynaCmsBundle:Admin/Settings/Seo:form.html.twig';
    }

    public function getName()
    {
        return 'ekyna_cms_settings_seo';
    }
}
