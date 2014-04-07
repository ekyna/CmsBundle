<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type\Block;

use Symfony\Component\Form\FormBuilderInterface;

class TinymceBlockType extends BlockType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('html', 'textarea', array(
        	'label' => false,
            'attr' => array(
                'class' => 'tinymce',
                'data-theme' => 'block',
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_tinymce_block';
    }
}
