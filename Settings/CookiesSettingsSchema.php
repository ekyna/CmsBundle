<?php

namespace Ekyna\Bundle\CmsBundle\Settings;

use Ekyna\Bundle\SettingBundle\Schema\AbstractSchema;
use Ekyna\Bundle\SettingBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Class CookiesSettingsSchema
 * @package Ekyna\Bundle\CmsBundle\Settings
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CookiesSettingsSchema extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array_merge(array(
                'mode'        => 'header',
                'header_text' => 'Default header content',
                'dialog_text' => 'Default dialog content',
            ), $this->defaults))
            ->setAllowedTypes(array(
                'mode'        => array('string'),
                'header_text' => array('string'),
                'dialog_text' => array('string'),
            ))
            ->setAllowedValues(array(
                'mode' => array('header', 'dialog'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mode', 'choice', array(
                'label'       => 'ekyna_cms.settings.cookies.mode.label',
                'choices'     => array(
                    'header' => 'ekyna_cms.settings.cookies.mode.header',
                    'dialog' => 'ekyna_cms.settings.cookies.mode.dialog',
                ),
                'constraints' => array(
                    new Constraints\NotNull()
                )
            ))
            ->add('header_text', 'text', array(
                'label'       => 'ekyna_cms.settings.cookies.header_text',
                'constraints' => array(
                    new Constraints\NotBlank()
                )
            ))
            ->add('dialog_text', 'textarea', array(
                'label'       => 'ekyna_cms.settings.cookies.dialog_text',
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
                'attr' => array(
                    'class' => 'tinymce',
                    'data-theme' => 'simple',
                ),
            ))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'ekyna_cms.settings.cookies.label';
    }

    /**
     * {@inheritDoc}
     */
    public function getShowTemplate()
    {
        return 'EkynaCmsBundle:Admin/Settings/Cookies:show.html.twig';
    }

    /**
     * {@inheritDoc}
     */
    public function getFormTemplate()
    {
        return 'EkynaCmsBundle:Admin/Settings/Cookies:form.html.twig';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ekyna_cms_settings_cookies';
    }
}
