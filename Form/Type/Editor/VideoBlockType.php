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
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class VideoBlockType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class VideoBlockType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var MediaRepository $repository */
        $repository = $options['repository'];

        $mediaTransformer = new CallbackTransformer(
            function (array $data) use ($repository) {
                if (!array_key_exists('media', $data)) {
                    $data['media'] = null;
                }
                if (0 < $mediaId = intval($data['media'])) {
                    $data['media'] = $repository->find($mediaId);
                }

                return $data;
            },
            function (array $data) {
                if (null !== $media = $data['media']) {
                    if ($media instanceof MediaInterface) {
                        $data['media'] = $media->getId();
                    } else {
                        throw new TransformationFailedException('Failed to reverse transform image block data.');
                    }
                }

                return $data;
            }
        );

        // Poster form
        $poster = $builder
            ->create('poster', null, [
                'label' => false,
                'compound' => true,
            ])
            ->add('media', MediaChoiceType::class, [
                'label'       => 'ekyna_core.field.image',
                'types'       => [MediaTypes::IMAGE],
                'constraints' => new AssertTypes([
                    'types' => [MediaTypes::IMAGE],
                ]),
            ])
            ->addModelTransformer($mediaTransformer);

        // Video form
        $video = $builder
            ->create('video', null, [
                'label' => false,
                'compound' => true,
            ])
            ->add('media', MediaChoiceType::class, [
                'label'       => 'ekyna_core.field.video',
                'types'       => [MediaTypes::VIDEO],
                'constraints' => new AssertTypes([
                    'types' => [MediaTypes::VIDEO],
                ]),
            ])
            ->add('autoplay', Type\CheckboxType::class, [
                'label'    => 'ekyna_cms.block.field.autoplay',
                'required' => false,
            ])
            ->add('loop', Type\CheckboxType::class, [
                'label'    => 'ekyna_cms.block.field.loop',
                'required' => false,
            ])
            ->add('muted', Type\CheckboxType::class, [
                'label'    => 'ekyna_cms.block.field.muted',
                'required' => false,
            ])
            ->add('player', Type\CheckboxType::class, [
                'label'    => 'ekyna_cms.block.field.player',
                'required' => false,
            ])
            ->addModelTransformer($mediaTransformer);

        $builder
            ->add($poster)
            ->add($video);
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
        return 'ekyna_cms_block_video';
    }
}
