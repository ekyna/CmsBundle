<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\CoreBundle\Form\Type\ColorPickerType;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BackgroundContainerType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BackgroundContainerType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->get('data')
            ->add('image', MediaChoiceType::class, [
                'label' => 'ekyna_core.field.image',
                'types' => [MediaTypes::IMAGE],
            ])
            ->add('video', MediaChoiceType::class, [
                'label' => 'ekyna_core.field.video',
                'types' => [MediaTypes::VIDEO],
            ])
            ->add('color', ColorPickerType::class, [
                'label' => 'ekyna_core.field.color',
            ]);

        if (!empty($themes = $options['themes'])) {
            $builder
                ->get('data')
                ->add('theme', ChoiceType::class, [
                    'label'       => 'ekyna_cms.block.field.theme',
                    'choices'     => array_flip($themes),
                    'placeholder' => 'ekyna_core.value.none',
                    'required'    => false,
                    'select2'     => false,
                ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('themes', [])
            ->setAllowedTypes('themes', 'array');
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_container_background';
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return BaseContainerType::class;
    }
}
