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
                'attr'  => array(
                    'data-collection-role' => 'position'
                ),
            ))
            ->add('image', 'ekyna_cms_image', array(
                'label'        => false,
                'file_path'    => $options['file_path'],
                'thumb_col'    => $options['thumb_col'],
                'rename_field' => $options['rename_field'],
                'alt_field'    => $options['alt_field'],
                'js_upload'    => $options['js_upload'],
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setDefaults(array(
                'file_path'    => 'path',
                'thumb_col'    => 3,
                'rename_field' => true,
                'alt_field'    => true,
                'js_upload'    => false,
            ))
            ->setOptional(array('file_path'))
            ->setAllowedTypes(array(
                'file_path'    => array('null', 'string'),
                'thumb_col'    => 'int',
                'rename_field' => 'bool',
                'alt_field'    => 'bool',
                'js_upload'    => 'bool',
            ))
            ->setNormalizers(array(
                'thumb_col' => function($options, $value) {
                    if (0 == strlen($options['file_path'])) {
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