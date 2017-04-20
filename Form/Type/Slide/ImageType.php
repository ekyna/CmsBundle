<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Slide;

use Ekyna\Bundle\MediaBundle\Form\Type\MediaChoiceType;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class ImageType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Slide
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ImageType extends AbstractType
{
    private MediaRepository $mediaRepository;

    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => t('field.image', [], 'EkynaUi'),
            'types' => [MediaTypes::IMAGE],
        ]);
    }

    public function getParent(): ?string
    {
        return MediaChoiceType::class;
    }
}
