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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ImageType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ImageType extends AbstractType
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
            ->add('media', MediaChoiceType::class, [
                'label'       => $options['media_label'],
                'types'       => $mediaTypes,
                'constraints' => new AssertTypes([
                    'types' => $mediaTypes,
                ]),
            ])
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
                'animations'  => $options['animations'],
            ]);

        $builder->addModelTransformer(new CallbackTransformer(
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
        ));
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('repository')
            ->setDefault('media_label', 'ekyna_core.field.image')
            ->setDefault('themes', [])
            ->setDefault('styles', [])
            ->setDefault('animations', [])
            ->setAllowedTypes('media_label', ['bool', 'string'])
            ->setAllowedTypes('repository', MediaRepository::class)
            ->setAllowedTypes('themes', 'array')
            ->setAllowedTypes('styles', 'array')
            ->setAllowedTypes('animations', 'array');
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_image';
    }
}
