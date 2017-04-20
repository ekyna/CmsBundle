<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

use function Symfony\Component\Translation\t;

/**
 * Class ImageBlockType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ImageBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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

    private function buildImageOptionsForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('align', Type\ChoiceType::class, [
                'label'                     => t('block.field.align', [], 'EkynaCms'),
                'choices'                   => [
                    'Left'   => 'left',
                    'Center' => 'center',
                    'Right'  => 'right',
                ],
                'choice_translation_domain' => false,
                'required'                  => true,
                'select2'                   => false,
            ])
            ->add('max_width', Type\TextType::class, [
                'label'       => t('block.field.max_width', [], 'EkynaCms'),
                'required'    => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\d+(px|%)$/'
                        // TODO message translation
                    ]),
                ],
            ])
            ->add('theme', Type\ChoiceType::class, [
                'label'                     => t('block.field.theme', [], 'EkynaCms'),
                'choices'                   => array_flip($options['themes']),
                'choice_translation_domain' => false,
                'placeholder'               => t('value.none', [], 'EkynaUi'),
                'required'                  => false,
                'select2'                   => false,
            ])
            ->add('style', Type\ChoiceType::class, [
                'label'                     => t('block.field.style', [], 'EkynaCms'),
                'choices'                   => array_flip($options['styles']),
                'choice_translation_domain' => false,
                'placeholder'               => t('value.none', [], 'EkynaUi'),
                'required'                  => false,
                'select2'                   => false,
            ])
            ->add('animation', AnimationType::class, [
                'animations' => $options['animations'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
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

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_block_image';
    }

    public function getParent(): ?string
    {
        return BaseBlockType::class;
    }
}
