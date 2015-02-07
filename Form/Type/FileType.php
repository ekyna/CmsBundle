<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;

/**
 * Class FileType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class FileType extends ResourceFormType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ekyna_core_file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_file';
    }
}
