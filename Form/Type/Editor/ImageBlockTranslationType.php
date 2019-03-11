<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;

/**
 * Class ImageBlockTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ImageBlockTranslationType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $mediaTypes = [MediaTypes::IMAGE, MediaTypes::SVG];

        // Data form
        $data = $builder
            ->create('data', null, [
                'label'    => false,
                'compound' => true,
            ])
            ->add('url', UrlType::class, [
                'label'       => 'ekyna_core.field.url',
                'required'    => false,
                'constraints' => [
                    new Url(),
                ],
            ])
            ->add('image', MediaChoiceType::class, [
                'label' => 'ekyna_core.field.image',
                'types' => $mediaTypes,
            ]);

        // Hover form
        if ($options['with_hover']) {
            $data->add('hover', MediaChoiceType::class, [
                'label' => 'ekyna_cms.block.field.hover',
                'types' => $mediaTypes,
            ]);
        }

        $builder->add($data);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('with_hover', true)
            ->setAllowedTypes('with_hover', 'bool');
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_block_image_translation';
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return BaseBlockTranslationType::class;
    }
}
