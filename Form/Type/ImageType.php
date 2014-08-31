<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ImageType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImageType extends AbstractType
{
    protected $dataClass;

    public function __construct($class)
    {
        $this->dataClass = $class;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
        ));
    }

    public function getParent()
    {
        return 'ekyna_core_image';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_image';
    }
}
