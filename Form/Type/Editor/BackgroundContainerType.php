<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\UiBundle\Form\Type\ColorPickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_flip;
use function Symfony\Component\Translation\t;

/**
 * Class BackgroundContainerType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BackgroundContainerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->get('data')
            ->add('image', MediaChoiceType::class, [
                'label'    => t('field.image', [], 'EkynaUi'),
                'types'    => [MediaTypes::IMAGE],
                'required' => false,
            ])
            ->add('video', MediaChoiceType::class, [
                'label'    => t('field.video', [], 'EkynaUi'),
                'types'    => [MediaTypes::VIDEO],
                'required' => false,
            ])
            ->add('color', ColorPickerType::class, [
                'label'    => t('field.color', [], 'EkynaUi'),
                'required' => false,
            ]);

        if (empty($themes = $options['themes'])) {
            return;
        }

        $builder
            ->get('data')
            ->add('theme', ChoiceType::class, [
                'label'                     => t('block.field.theme', [], 'EkynaCms'),
                'choices'                   => array_flip($themes),
                'choice_translation_domain' => false,
                'placeholder'               => t('value.none', [], 'EkynaUi'),
                'required'                  => false,
                'select2'                   => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('themes', [])
            ->setAllowedTypes('themes', 'array');
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_container_background';
    }

    public function getParent(): ?string
    {
        return BaseContainerType::class;
    }
}
