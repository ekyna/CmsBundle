<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ImageType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GalleryType extends ResourceFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['name_field']) {
            $builder
                ->add('name', 'text', array(
                    'label' => 'ekyna_core.field.name',
                ))
            ;
        }

        $builder
            ->add('images', 'ekyna_core_collection', array(
                'label'        => 'ekyna_core.field.images',
                'type'         => 'ekyna_cms_gallery_image',
                'allow_add'    => $options['allow_add'],
                'allow_delete' => $options['allow_delete'],
                'allow_sort'   => $options['allow_sort'],
                'add_button_text' => 'ekyna_core.button.add',
                'sub_widget_col'  => 11,
                'button_col'      => 1,
                'prototype_name'  => '__iname__',
                'options'      => array(
                    'label'        => false,
                    'required'     => false,
                    'file_path'    => $options['file_path'],
                    'thumb_col'    => $options['thumb_col'],
                    'rename_field' => $options['rename_field'],
                    'alt_field'    => $options['alt_field'],
                    'js_upload'    => $options['js_upload'],
                    'attr'         => array(
                        'widget_col' => 12
                    ),
                ),
                'attr'         => array(
                    'widget_col' => 10
                ),
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
                'allow_add'    => true,
                'allow_delete' => true,
                'allow_sort'   => true,
                'name_field'   => false,
                'file_path'    => 'path',
                'thumb_col'    => 3,
                'rename_field' => true,
                'alt_field'    => true,
                'required'     => false,
                'js_upload'    => false,
            ))
            ->setOptional(array('file_path'))
            ->setAllowedTypes(array(
                'allow_add'    => 'bool',
                'allow_delete' => 'bool',
                'allow_sort'   => 'bool',
                'name_field'   => 'bool',
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
        return 'ekyna_cms_gallery';
    }
}
