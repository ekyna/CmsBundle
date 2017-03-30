<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AnimationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class AnimationType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Type\ChoiceType::class, [
                'label'       => 'ekyna_cms.block.field.animation',
                'choices'     => array_flip($options['animations']),
                'placeholder' => 'ekyna_core.value.none',
                'sizing'      => 'sm',
                'required'    => false,
                'select2'     => false,
            ])
            ->add('offset', Type\IntegerType::class, [
                'label'    => 'ekyna_cms.block.field.offset',
                'sizing'      => 'sm',
                'required' => false,
            ])
            ->add('duration', Type\IntegerType::class, [
                'label'    => 'ekyna_cms.block.field.duration',
                'sizing'      => 'sm',
                'required' => false,
            ])
            ->add('once', Type\CheckboxType::class, [
                'label'    => 'ekyna_cms.block.field.once',
                'sizing'      => 'sm',
                'required' => false,
                'attr'     => [
                    'align_with_widget' => true,
                ],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('label', false)
            ->setRequired('animations')
            ->setAllowedTypes('animations', 'array');
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_animation';
    }
}
