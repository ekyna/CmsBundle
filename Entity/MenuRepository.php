<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Ekyna\Component\Resource\Doctrine\ORM\TranslatableResourceRepositoryInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Util\TranslatableResourceRepositoryTrait;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Class MenuRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuRepository extends NestedTreeRepository implements TranslatableResourceRepositoryInterface
{
    use TranslatableResourceRepositoryTrait;

    /**
     * Finds the menu by his name, optionally filtered by root ("rootName:menuName" format).
     *
     * @param string $name
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\MenuInterface|null
     * @throws \InvalidArgumentException
     */
    public function findOneByName($name)
    {
        $as = $this->getAlias();
        $qb = $this->getQueryBuilder();

        $qb->andWhere($qb->expr()->eq($as . '.name', ':name'));

        $parameters = ['name' => $name];

        if (0 < strpos($name, ':')) {
            list($rootName, $menuName) = explode(':', $name);

            /** @var \Ekyna\Bundle\CmsBundle\Model\MenuInterface $root */
            if (null === $root = $this->findOneBy(['name' => $rootName])) {
                throw new \InvalidArgumentException(sprintf('Root menu "%s" not found.', $rootName));
            }

            $qb->andWhere($qb->expr()->eq($as . '.root', ':root'));

            $parameters = [
                'name' => $menuName,
                'root' => $root,
            ];
        }

        return $qb
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600, Menu::getEntityTagPrefix() . '[name=' . $name . ']')
            ->setMaxResults(1)
            ->setParameters($parameters)
            ->getOneOrNullResult();
    }

    /**
     * Returns the menus data for the menu provider.
     *
     * @return array
     */
    public function findForProvider()
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->select(
                'm.id, IDENTITY(m.parent) as parent, m.name, m.route, m.parameters, m.root, '.
                'm.attributes, m.options, t.title, t.path'
            )
            ->leftJoin('m.translations', 't', Expr\Join::WITH, $qb->expr()->eq('t.locale', ':locale'))
            ->andWhere($qb->expr()->eq('m.enabled', ':enabled'))
            ->orderBy('m.left', 'ASC')
            ->addGroupBy('m.id');

        return $qb
            ->getQuery()
            ->useQueryCache(true)
            //->useResultCache(true, 3600, Menu::getEntityTagPrefix()) // TODO Use a more unique cache id and invalidate it.
            ->setParameters([
                'locale'  => $this->localeProvider->getCurrentLocale(),
                'enabled' => true,
            ])
            ->getArrayResult();
    }

    /**
     * @inheritdoc
     */
    protected function getAlias()
    {
        return 'm';
    }
}
