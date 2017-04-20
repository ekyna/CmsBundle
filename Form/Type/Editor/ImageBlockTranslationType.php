<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;

use function Symfony\Component\Translation\t;

/**
 * Class ImageBlockTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ImageBlockTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mediaTypes = [MediaTypes::IMAGE, MediaTypes::SVG];

        // Data form
        $data = $builder
            ->create('data', null, [
                'label'    => false,
                'compound' => true,
            ])
            ->add('url', UrlType::class, [
                'label'       => t('field.url', [], 'EkynaUi'),
                'required'    => false,
                'constraints' => [
                    new Url(),
                ],
            ])
            ->add('image', MediaChoiceType::class, [
                'label' => t('field.image', [], 'EkynaUi'),
                'types' => $mediaTypes,
            ]);

        // Hover form
        if ($options['with_hover']) {
            $data->add('hover', MediaChoiceType::class, [
                'label' => t('block.field.hover', [], 'EkynaCms'),
                'types' => $mediaTypes,
            ]);
        }

        $builder->add($data);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('with_hover', true)
            ->setAllowedTypes('with_hover', 'bool');
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_block_image_translation';
    }

    public function getParent(): ?string
    {
        return BaseBlockTranslationType::class;
    }
}
