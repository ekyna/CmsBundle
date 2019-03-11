<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Form\Type\MediaChoiceType as ChoiceType;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Validator\Constraints\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MediaChoiceType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaChoiceType extends AbstractType
{
    /**
     * @var MediaRepository
     */
    private $mediaRepository;


    /**
     * Constructor.
     *
     * @param MediaRepository $mediaRepository
     */
    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('media', ChoiceType::class, [
                'label'       => false,
                'types'       => $options['types'],
                'constraints' => [
                    new MediaTypes([
                        'types' => $options['types'],
                    ]),
                ],
            ])
            ->addModelTransformer(new CallbackTransformer(
                function (array $data = null) {
                    $data = array_replace([
                        'media' => null,
                    ], $data ?? []);

                    if (0 < $mediaId = intval($data['media'])) {
                        $data['media'] = $this->mediaRepository->find($mediaId);
                    }

                    return $data;
                },
                function (array $data = null) {
                    $data = array_replace([
                        'media' => null,
                    ], $data ?? []);

                    if (null !== $media = $data['media']) {
                        if ($media instanceof MediaInterface) {
                            $data['media'] = $media->getId();
                        } else {
                            throw new TransformationFailedException('Failed to reverse transform block media data.');
                        }
                    }

                    return $data;
                }
            ));
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('types');
    }
}
