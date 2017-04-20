<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class AnimationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class AnimationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\ChoiceType::class, [
                'label'                     => t('block.field.animation', [], 'EkynaCms'),
                'choices'                   => array_flip($options['animations']),
                'choice_translation_domain' => false,
                'placeholder'               => t('value.none', [], 'EkynaUi'),
                'required'                  => false,
                'select2'                   => false,
            ])
            ->add('offset', Type\IntegerType::class, [
                'label'    => t('block.field.offset', [], 'EkynaCms'),
                'required' => false,
            ])
            ->add('duration', Type\IntegerType::class, [
                'label'    => t('block.field.duration', [], 'EkynaCms'),
                'required' => false,
            ])
            ->add('once', Type\CheckboxType::class, [
                'label'    => t('block.field.once', [], 'EkynaCms'),
                'required' => false,
                'attr'     => [
                    'align_with_widget' => true,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('label', false)
            ->setRequired('animations')
            ->setAllowedTypes('animations', 'array');
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_animation';
    }
}
