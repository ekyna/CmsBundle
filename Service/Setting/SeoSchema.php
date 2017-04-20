<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Setting;

use Ekyna\Bundle\SettingBundle\Form\Type\I18nParameterType;
use Ekyna\Bundle\SettingBundle\Model\I18nParameter;
use Ekyna\Bundle\SettingBundle\Schema\AbstractSchema;
use Ekyna\Bundle\SettingBundle\Schema\LocalizedSchemaInterface;
use Ekyna\Bundle\SettingBundle\Schema\LocalizedSchemaTrait;
use Ekyna\Bundle\SettingBundle\Schema\SettingsBuilder;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Contracts\Translation\TranslatableInterface;

use function Symfony\Component\Translation\t;

/**
 * Class SeoSchema
 * @package Ekyna\Bundle\CmsBundle\Service\Setting
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SeoSchema extends AbstractSchema implements LocalizedSchemaInterface
{
    use LocalizedSchemaTrait;

    public function buildSettings(SettingsBuilder $builder): void
    {
        $builder
            ->setDefaults(array_merge([
                'title'       => $this->createI18nParameter('Page default title'),
                'description' => $this->createI18nParameter('Page default description'),
            ], $this->defaults))
            ->setAllowedTypes('title', I18nParameter::class)
            ->setAllowedTypes('description', I18nParameter::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', I18nParameterType::class, [
                'label'        => t('field.title', [], 'EkynaUi'),
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
                'label'        => t('field.description', [], 'EkynaUi'),
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

    public function getLabel(): TranslatableInterface
    {
        return t('field.seo', [], 'EkynaUi');
    }

    public function getShowTemplate(): string
    {
        return '@EkynaCms/Admin/Settings/Seo/show.html.twig';
    }

    public function getFormTemplate(): string
    {
        return '@EkynaCms/Admin/Settings/Seo/form.html.twig';
    }
}
