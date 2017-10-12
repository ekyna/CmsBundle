<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Slide;

use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Form\Type\MediaChoiceType;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImageType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Slide
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ImageType extends AbstractType
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
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($data) { // Transform
                if (0 < $data) {
                    return $this->mediaRepository->find($data);
                }

                return null;
            },
            function ($data) { // Reverse transform
                if ($data instanceof MediaInterface) {
                    return $data->getId();
                }

                return null;
            }
        ));
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'ekyna_core.field.image',
            'types' => [MediaTypes::IMAGE],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return MediaChoiceType::class;
    }
}
