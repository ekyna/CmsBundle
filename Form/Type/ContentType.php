<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * ContentType
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ContentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('blocks', 'ekyna_cms_block_collection', array(
                'label' => false,
                'types' => array(
                    'ekyna_cms_text_block', // The first defined Type becomes the default
                    'ekyna_cms_tinymce_block',
                    'ekyna_cms_image_block',
                ),
                'allow_add' => true,
                'allow_delete' => true,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class'  => 'Ekyna\Bundle\CmsBundle\Entity\Content'));
    }

    public function getName()
    {
    	return 'ekyna_cms_content';
    }
}
