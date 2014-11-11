<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class BlockRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class BlockRepository extends EntityRepository
{
    /**
     * @param $name
     * @return \Ekyna\Bundle\CmsBundle\Model\BlockInterface|null
     */
    public function findOneByName($name)
    {
        return $this->findOneBy(array('name' => $name, 'content' => null));
    }
}
