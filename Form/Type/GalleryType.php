<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;

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
        $builder
            ->add('name', 'text', array(
                'label' => 'ekyna_core.field.name',
            ))
            ->add('images', 'collection', array(
                'label'        => 'ekyna_core.field.images',
                'type'         => 'ekyna_cms_gallery_image',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'options'      => array(
                    'label' => false,
                    'required' => false,
                    'attr' => array(
                        'widget_col' => 12
                    ),
                    'data_class' => 'Ekyna\Bundle\CmsBundle\Entity\GalleryImage',
                )
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
