<?php

namespace Ekyna\Bundle\CmsBundle\Settings;

use Ekyna\Bundle\CoreBundle\Form\Type\TinymceType;
use Ekyna\Bundle\SettingBundle\Form\Type\I18nParameterType;
use Ekyna\Bundle\SettingBundle\Model\I18nParameter;
use Ekyna\Bundle\SettingBundle\Schema\AbstractSchema;
use Ekyna\Bundle\SettingBundle\Schema\LocalizedSchemaInterface;
use Ekyna\Bundle\SettingBundle\Schema\LocalizedSchemaTrait;
use Ekyna\Bundle\SettingBundle\Schema\SettingsBuilder;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class CookiesSettingsSchema
 * @package Ekyna\Bundle\CmsBundle\Settings
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CookiesSettingsSchema extends AbstractSchema implements LocalizedSchemaInterface
{
    use LocalizedSchemaTrait;

    /**
     * @inheritdoc
     */
    public function buildSettings(SettingsBuilder $builder)
    {
        $builder
            ->setDefaults(array_merge([
                'mode'        => 'header',
                'header_text' => $this->createI18nParameter('Default header content'),
                'dialog_text' => $this->createI18nParameter('<p>Default dialog content</p>'),
            ], $this->defaults))
            ->setAllowedTypes('mode', 'string')
            ->setAllowedTypes('header_text', I18nParameter::class)
            ->setAllowedTypes('dialog_text', I18nParameter::class)
            ->setAllowedValues('mode', ['header', 'dialog']);
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mode', Type\ChoiceType::class, [
                'label'       => 'ekyna_cms.settings.cookies.mode.label',
                'choices'     => [
                    'ekyna_cms.settings.cookies.mode.header' => 'header',
                    'ekyna_cms.settings.cookies.mode.dialog' => 'dialog',
                ],
                'constraints' => [
                    new Constraints\NotNull(),
                ],
            ])
            ->add('header_text', I18nParameterType::class, [
                'label'        => 'ekyna_cms.settings.cookies.header_text',
                'form_type'    => Type\TextareaType::class,
                'form_options' => [
                    'label'       => false,
                    'constraints' => [
                        new Constraints\NotBlank(),
                    ],
                ],
                'constraints'  => [
                    new Valid(),
                ],
            ])
            ->add('dialog_text', I18nParameterType::class, [
                'label'        => 'ekyna_cms.settings.cookies.dialog_text',
                'form_type'    => TinymceType::class,
                'form_options' => [
                    'label'       => false,
                    'theme'       => 'simple',
                    'constraints' => [
                        new Constraints\NotBlank(),
                    ],
                ],
                'constraints'  => [
                    new Valid(),
                ],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return 'ekyna_cms.settings.cookies.label';
    }

    /**
     * @inheritdoc
     */
    public function getShowTemplate()
    {
        return '@EkynaCms/Admin/Settings/Cookies/show.html.twig';
    }

    /**
     * @inheritdoc
     */
    public function getFormTemplate()
    {
        return '@EkynaCms/Admin/Settings/Cookies/form.html.twig';
    }
}
