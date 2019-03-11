<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Editor;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class VideoBlockTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class VideoBlockTranslationType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Data form
        $data = $builder
            ->create('data', null, [
                'label'    => false,
                'compound' => true,
            ])
            ->add('poster', MediaChoiceType::class, [
                'label' => 'ekyna_core.field.image',
                'types' => [MediaTypes::IMAGE],
            ])
            ->add('video', MediaChoiceType::class, [
                'label' => 'ekyna_core.field.video',
                'types' => [MediaTypes::VIDEO],
            ]);

        $builder->add($data);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return BaseBlockTranslationType::class;
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_cms_block_video_translation';
    }
}
