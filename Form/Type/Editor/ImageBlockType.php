<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ImageBlockType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ImageBlockType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Image form
        $image = $builder->create('image', null, [
            'label'    => 'Image options', // TODO trans
            'compound' => true,
        ]);

        $this->buildImageOptionsForm($image, $options);

        $builder->get('data')->add($image);

        // Hover form
        if ($options['with_hover']) {
            $hover = $builder->create('hover', null, [
                'label'    => false,
                'compound' => true,
            ]);

            $this->buildImageOptionsForm($hover, $options);

            $builder->get('data')->add($hover);
        }

        $builder
            ->add('translations', TranslationsFormsType::class, [
                'form_type'      => ImageBlockTranslationType::class,
                'label'          => false,
                'error_bubbling' => false,
            ]);
    }

    /**
     * Builds the image options form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    private function buildImageOptionsForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('align', Type\ChoiceType::class, [
                'label'    => 'ekyna_cms.block.field.align',
                'choices'  => [
                    'Left'   => 'left',
                    'Center' => 'center',
                    'Right'  => 'right',
                ],
                'required' => true,
                'select2'  => false,
            ])
            ->add('max_width', Type\TextType::class, [
                'label'       => 'ekyna_cms.block.field.max_width',
                'required'    => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\d+(px|%)$/'
                        // TODO message translation
                    ]),
                ],
            ])
            ->add('theme', Type\ChoiceType::class, [
                'label'       => 'ekyna_cms.block.field.theme',
                'choices'     => array_flip($options['themes']),
                'placeholder' => 'ekyna_core.value.none',
                'required'    => false,
                'select2'     => false,
            ])
            ->add('style', Type\ChoiceType::class, [
                'label'       => 'ekyna_cms.block.field.style',
                'choices'     => array_flip($options['styles']),
                'placeholder' => 'ekyna_core.value.none',
                'required'    => false,
                'select2'     => false,
            ])
            ->add('animation', AnimationType::class, [
                'animations' => $options['animations'],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('themes', [])
            ->setDefault('styles', [])
            ->setDefault('animations', [])
            ->setDefault('with_hover', true)
            ->setAllowedTypes('themes', 'array')
            ->setAllowedTypes('styles', 'array')
            ->setAllowedTypes('animations', 'array')
            ->setAllowedTypes('with_hover', 'bool');
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_block_image';
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return BaseBlockType::class;
    }
}
