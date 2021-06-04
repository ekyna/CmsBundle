<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\MediaBundle\Model\AspectRatio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

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
        // Video form
        $video = $builder
            ->create('video', null, [
                'label'    => false,
                'compound' => true,
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
            ->add('ratio', Type\ChoiceType::class, [
                'label'    => 'ekyna_core.field.format',
                'choices'  => AspectRatio::getChoices(),
                'required' => true,
            ])
            ->add('height', Type\TextType::class, [
                'label'    => 'ekyna_core.field.height',
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

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return BaseBlockType::class;
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_block_video';
    }
}
