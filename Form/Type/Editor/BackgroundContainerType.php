<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\CoreBundle\Form\Type\ColorPickerType;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Form\Type\MediaChoiceType;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
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
        /** @var MediaRepository $repository */
        $repository = $options['repository'];

        $builder
            ->add('media_id', MediaChoiceType::class, [
                'label' => 'ekyna_core.field.image',
                'types' => [MediaTypes::IMAGE],
            ])
            ->add('video_id', MediaChoiceType::class, [
                'label' => 'ekyna_core.field.video',
                'types' => [MediaTypes::VIDEO],
            ])
            ->add('color', ColorPickerType::class, [
                'label' => 'ekyna_core.field.color',
            ]);

        $builder->addModelTransformer(new CallbackTransformer(
            // Transform
            function(array $data) use ($repository) {
                if (!array_key_exists('media_id', $data)) {
                    $data['media_id'] = null;
                }
                if (0 < $mediaId = intval($data['media_id'])) {
                    $data['media_id'] = $repository->find($mediaId);
                }

                if (!array_key_exists('video_id', $data)) {
                    $data['video_id'] = null;
                }
                if (0 < $videoId = intval($data['video_id'])) {
                    $data['video_id'] = $repository->find($videoId);
                }

                return $data;
            },
            // Reverse transform
            function(array $data) {
                if (null !== $media = $data['media_id']) {
                    if ($media instanceof MediaInterface) {
                        $data['media_id'] = $media->getId();
                    } else {
                        throw new TransformationFailedException('Failed to reverse transform image block data.');
                    }
                }

                if (null !== $video = $data['video_id']) {
                    if ($video instanceof MediaInterface) {
                        $data['video_id'] = $video->getId();
                    } else {
                        throw new TransformationFailedException('Failed to reverse transform video block data.');
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
            ->setAllowedTypes('repository', MediaRepository::class);
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_container_background';
    }
}
