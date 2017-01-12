<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Form\Type\MediaChoiceType;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FeatureBlockType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class FeatureBlockType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('style', ChoiceType::class, [
                'label'       => 'ekyna_core.field.style',
                'choices'     => array_flip($options['style_choices']),
                'placeholder' => 'ekyna_core.value.none',
                'required'    => false,
            ])
            ->add('animation', ChoiceType::class, [
                'label'       => 'ekyna_core.field.animation',
                'choices'     => array_flip($options['animation_choices']),
                'placeholder' => 'ekyna_core.value.none',
                'required'    => false,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('style_choices')
            ->setRequired('animation_choices')
            ->setAllowedTypes('style_choices', 'array')
            ->setAllowedTypes('animation_choices', 'array');
    }
}
