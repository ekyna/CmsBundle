<?php

namespace Ekyna\Bundle\CmsBundle\Settings;

use Ekyna\Bundle\SettingBundle\Schema\AbstractSchema;
use Ekyna\Bundle\SettingBundle\Schema\SettingsBuilder;
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
    public function buildSettings(SettingsBuilder $builder)
    {
        $builder
            ->setDefaults(array_merge([
                'mode'        => 'header',
                'header_text' => 'Default header content',
                'dialog_text' => 'Default dialog content',
            ], $this->defaults))
            ->setAllowedTypes('mode',         'string')
            ->setAllowedTypes('header_text',  'string')
            ->setAllowedTypes('dialog_text',  'string')
            ->setAllowedValues('mode', ['header', 'dialog'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mode', 'choice', [
                'label'       => 'ekyna_cms.settings.cookies.mode.label',
                'choices'     => [
                    'header' => 'ekyna_cms.settings.cookies.mode.header',
                    'dialog' => 'ekyna_cms.settings.cookies.mode.dialog',
                ],
                'constraints' => [
                    new Constraints\NotNull()
                ]
            ])
            ->add('header_text', 'text', [
                'label'       => 'ekyna_cms.settings.cookies.header_text',
                'constraints' => [
                    new Constraints\NotBlank()
                ]
            ])
            ->add('dialog_text', 'textarea', [
                'label'       => 'ekyna_cms.settings.cookies.dialog_text',
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
                'attr' => [
                    'class' => 'tinymce',
                    'data-theme' => 'simple',
                ],
            ])
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
