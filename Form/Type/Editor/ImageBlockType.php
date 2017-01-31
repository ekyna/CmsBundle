<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Form\Type\MediaChoiceType;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Validator\Constraints\MediaTypes as AssertTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;

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
        /** @var MediaRepository $repository */
        $repository = $options['repository'];

        $mediaTypes = [MediaTypes::IMAGE, MediaTypes::SVG];

        $builder
            ->add('media_id', MediaChoiceType::class, [
                'label'       => 'ekyna_core.field.image',
                'types'       => [MediaTypes::IMAGE, MediaTypes::SVG],
                'constraints' => new AssertTypes([
                    'types' => $mediaTypes,
                ]),
            ])
            ->add('url', UrlType::class, [
                'label'       => 'ekyna_core.field.url',
                'required'    => false,
                'constraints' => [
                    new Url(),
                ],
            ]);

        if ($options['with_hover']) {
            $builder->add('hover_id', MediaChoiceType::class, [
                'label'       => 'ekyna_cms.block.plugin.image.hover',
                'types'       => [MediaTypes::IMAGE, MediaTypes::SVG],
                'constraints' => new AssertTypes([
                    'types' => $mediaTypes,
                ]),
            ]);
        }

        if (is_array($options['style_choices']) && !empty($options['style_choices'])) {
            $builder->add('style', ChoiceType::class, [
                'label'       => 'ekyna_core.field.style',
                'choices'     => array_flip($options['style_choices']),
                'placeholder' => 'ekyna_core.value.none',
                'required'    => false,
            ]);
        }

        $builder->addModelTransformer(new CallbackTransformer(
            // Transform
            function (array $data) use ($repository) {
                foreach (['media_id', 'hover_id'] as $field) {
                    if (!array_key_exists($field, $data)) {
                        $data[$field] = null;
                    }
                    if (0 < $mediaId = intval($data[$field])) {
                        $data[$field] = $repository->find($mediaId);
                    }
                }

                return $data;
            },
            // Reverse transform
            function (array $data) {
                foreach (['media_id', 'hover_id'] as $field) {
                    if (null !== $media = $data[$field]) {
                        if ($media instanceof MediaInterface) {
                            $data[$field] = $media->getId();
                        } else {
                            throw new TransformationFailedException('Failed to reverse transform image block data.');
                        }
                    }
                }

                return $data;
            }
        ));
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('repository')
            ->setDefault('style_choices', null)
            ->setDefault('with_hover', false)
            ->setAllowedTypes('repository', MediaRepository::class)
            ->setAllowedTypes('style_choices', ['null', 'array'])
            ->setAllowedTypes('with_hover', 'bool');
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_block_image';
    }
}
