<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Component\Resource\Doctrine\ORM\TranslatableResourceRepository;

/**
 * Class BlockRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BlockRepository extends TranslatableResourceRepository
{
    /**
     * Finds the block by name
     *
     * @param string $name
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\BlockInterface|null
     */
    public function findOneByName($name)
    {
        return $this->findOneBy(['name' => $name, 'content' => null]);
    }
}
