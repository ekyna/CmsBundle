<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class GalleryImageType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GalleryImageType extends ResourceFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', 'hidden', array(
                'label' => false,
                'attr'  => array(
                    'data-role' => 'position'
                ),
            ))
            ->add('image', 'ekyna_cms_image', array(
                'label'        => false,
                'image_path'   => $options['image_path'],
                'thumb_col'    => $options['thumb_col'],
                'rename_field' => $options['rename_field'],
                'alt_field'    => $options['alt_field'],
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'    => 'Ekyna\Bundle\CmsBundle\Entity\GalleryImage',
                'image_path'    => 'path',
                'thumb_col'     => 3,
                'rename_field'  => true,
                'alt_field'     => true,
            ))
            ->setRequired(array('data_class'))
            ->setOptional(array('image_path'))
            ->setAllowedTypes(array(
                'image_path'    => array('null', 'string'),
                'thumb_col'     => 'int',
                'rename_field'  => 'bool',
                'alt_field'     => 'bool',
            ))
            ->setNormalizers(array(
                'thumb_col' => function($options, $value) {
                    if (0 == strlen($options['image_path'])) {
                        return 0;
                    }
                    if ($value > 4) {
                        return 4;
                    }
                    return $value;
                },
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_gallery_image';
    }
}