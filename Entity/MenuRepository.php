<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\AdminBundle\Doctrine\ORM\TranslatableResourceRepositoryInterface;
use Ekyna\Bundle\AdminBundle\Doctrine\ORM\Util\TranslatableResourceRepositoryTrait;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Class MenuRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuRepository extends NestedTreeRepository implements TranslatableResourceRepositoryInterface
{
    use TranslatableResourceRepositoryTrait;

    /**
     * Finds the menu by his name, optionally filtered by root ("rootName:menuName" notation).
     * @param string $name
     * @return \Ekyna\Bundle\CmsBundle\Model\MenuInterface|null
     * @throws \InvalidArgumentException
     */
    public function findOneByName($name)
    {
        if (0 < strpos($name, ':')) {
            list($rootName, $menuName) = explode(':', $name);
            /** @var \Ekyna\Bundle\CmsBundle\Model\MenuInterface $root */
            if (null === $root = $this->findOneBy(array('name' => $rootName))) {
                throw new \InvalidArgumentException(sprintf('Root menu "%s" not found.', $rootName));
            }
            return $this->findOneBy(array(
                'name' => $menuName,
                'root' => $root->getId(),
            ));
        }

        return $this->findOneBy(array('name' => $name));
    }
}
