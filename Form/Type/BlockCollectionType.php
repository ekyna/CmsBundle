<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * BlockCollectionType
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class BlockCollectionType extends AbstractType
{
    public function getParent()
    {
        return 'infinite_form_polycollection';
    }
    
    public function getName()
    {
        return 'ekyna_cms_block_collection';
    }
}
