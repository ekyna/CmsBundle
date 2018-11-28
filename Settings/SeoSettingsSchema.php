<?php

namespace Ekyna\Bundle\CmsBundle\Settings;

use Ekyna\Bundle\SettingBundle\Schema\AbstractSchema;
use Ekyna\Bundle\SettingBundle\Schema\SettingsBuilder;
use Symfony\Component\Form\Extension\Core\Type;
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
     * @inheritdoc
     */
    public function buildSettings(SettingsBuilder $builder)
    {
        $builder
            ->setDefaults(array_merge([
                'title'       => 'Page default title',
                'description' => 'Page default description',
                'locale'      => 'fr',
            ], $this->defaults))
            ->setAllowedTypes('title',        'string')
            ->setAllowedTypes('description',  'string')
            ->setAllowedTypes('locale',       'string')
        ;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', Type\TextType::class, [
                'label'       => 'ekyna_core.field.title',
                'constraints' => [
                    new Constraints\NotBlank()
                ]
            ])
            ->add('description', Type\TextareaType::class, [
                'label'       => 'ekyna_core.field.description',
                'constraints' => [
                    new Constraints\NotBlank()
                ]
            ])
            ->add('locale', Type\LocaleType::class, [
                'label'       => 'ekyna_core.field.locale',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Locale(),
                ]
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return 'ekyna_core.field.seo';
    }

    /**
     * @inheritdoc
     */
    public function getShowTemplate()
    {
        return '@EkynaCms/Admin/Settings/Seo/show.html.twig';
    }

    /**
     * @inheritdoc
     */
    public function getFormTemplate()
    {
        return '@EkynaCms/Admin/Settings/Seo/form.html.twig';
    }
}
