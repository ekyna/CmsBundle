<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use function Symfony\Component\Translation\t;

/**
 * Class VideoBlockTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class VideoBlockTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Data form
        $data = $builder
            ->create('data', null, [
                'label'    => false,
                'compound' => true,
            ])
            ->add('poster', MediaChoiceType::class, [
                'label' => t('field.image', [], 'EkynaUi'),
                'types' => [MediaTypes::IMAGE],
            ])
            ->add('video', MediaChoiceType::class, [
                'label' => t('field.video', [], 'EkynaUi'),
                'types' => [MediaTypes::VIDEO],
            ]);

        $builder->add($data);
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_block_video_translation';
    }

    public function getParent(): ?string
    {
        return BaseBlockTranslationType::class;
    }
}
