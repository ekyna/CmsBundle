<?php

namespace Ekyna\Bundle\CmsBundle\Settings;

use Ekyna\Bundle\SettingBundle\Form\Type\I18nParameterType;
use Ekyna\Bundle\SettingBundle\Model\I18nParameter;
use Ekyna\Bundle\SettingBundle\Schema\AbstractSchema;
use Ekyna\Bundle\SettingBundle\Schema\LocalizedSchemaInterface;
use Ekyna\Bundle\SettingBundle\Schema\LocalizedSchemaTrait;
use Ekyna\Bundle\SettingBundle\Schema\SettingsBuilder;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SeoSettingsSchema
 * @package Ekyna\Bundle\CmsBundle\Settings
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SeoSettingsSchema extends AbstractSchema implements LocalizedSchemaInterface
{
    use LocalizedSchemaTrait;

    /**
     * @inheritDoc
     */
    public function buildSettings(SettingsBuilder $builder)
    {
        $builder
            ->setDefaults(array_merge([
                'title'       => $this->createI18nParameter('Page default title'),
                'description' => $this->createI18nParameter('Page default description'),
            ], $this->defaults))
            ->setAllowedTypes('title', I18nParameter::class)
            ->setAllowedTypes('description', I18nParameter::class);
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', I18nParameterType::class, [
                'label'        => 'ekyna_core.field.title',
                'form_type'    => Type\TextType::class,
                'form_options' => [
                    'label'       => false,
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ],
                'constraints'  => [
                    new Assert\Valid(),
                ],
            ])
            ->add('description', I18nParameterType::class, [
                'label'        => 'ekyna_core.field.description',
                'form_type'    => Type\TextType::class,
                'form_options' => [
                    'label'       => false,
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ],
                'constraints'  => [
                    new Assert\Valid(),
                ],
            ]);
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return 'ekyna_core.field.seo';
    }

    /**
     * @inheritDoc
     */
    public function getShowTemplate()
    {
        return '@EkynaCms/Admin/Settings/Seo/show.html.twig';
    }

    /**
     * @inheritDoc
     */
    public function getFormTemplate()
    {
        return '@EkynaCms/Admin/Settings/Seo/form.html.twig';
    }
}
