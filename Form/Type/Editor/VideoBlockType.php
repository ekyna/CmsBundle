<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\MediaBundle\Model\AspectRatio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

use function Symfony\Component\Translation\t;

/**
 * Class VideoBlockType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class VideoBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Video form
        $video = $builder
            ->create('video', null, [
                'label'    => false,
                'compound' => true,
            ])
            ->add('autoplay', Type\CheckboxType::class, [
                'label'    => t('block.field.autoplay', [], 'EkynaCms'),
                'required' => false,
            ])
            ->add('loop', Type\CheckboxType::class, [
                'label'    => t('block.field.loop', [], 'EkynaCms'),
                'required' => false,
            ])
            ->add('muted', Type\CheckboxType::class, [
                'label'    => t('block.field.muted', [], 'EkynaCms'),
                'required' => false,
            ])
            ->add('player', Type\CheckboxType::class, [
                'label'    => t('block.field.player', [], 'EkynaCms'),
                'required' => false,
            ])
            ->add('ratio', Type\ChoiceType::class, [
                'label'    => t('field.format', [], 'EkynaUi'),
                'choices'  => AspectRatio::getChoices(),
                'required' => true,
            ])
            ->add('height', Type\TextType::class, [
                'label'    => t('field.height', [], 'EkynaUi'),
                'required' => false,
            ]);

        // Data form
        $data = $builder
            ->create('data', null, [
                'label'    => false,
                'compound' => true,
            ])
            ->add($video);

        $builder
            ->add($data)
            ->add('translations', TranslationsFormsType::class, [
                'form_type'      => VideoBlockTranslationType::class,
                'label'          => false,
                'error_bubbling' => false,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_cms_block_video';
    }

    public function getParent(): ?string
    {
        return BaseBlockType::class;
    }
}
