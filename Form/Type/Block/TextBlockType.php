<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Block;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * TextBlockType
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class TextBlockType extends BlockType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('text', 'text', array(
        	'label' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_text_block';
    }
}
