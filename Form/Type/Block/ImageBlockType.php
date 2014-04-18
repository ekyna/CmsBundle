<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Block;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * ImageBlockType
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImageBlockType extends BlockType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ekyna_core_image';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_image_block';
    }
}
