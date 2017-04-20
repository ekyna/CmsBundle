<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use Doctrine\ORM\Query\Expr;
use Ekyna\Bundle\CmsBundle\Exception\InvalidArgumentException;
use Ekyna\Bundle\CmsBundle\Model\MenuInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\TranslatableRepository;

/**
 * Class MenuRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuRepository extends TranslatableRepository implements MenuRepositoryInterface
{
    /**
     * Finds the menu by his name, optionally filtered by root ("rootName:menuName" format).
     *
     * @param string $name
     *
     * @return MenuInterface|null
     * @throws InvalidArgumentException
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function findOneByName(string $name): ?MenuInterface
    {
        $as = $this->getAlias();
        $qb = $this->getQueryBuilder();

        $qb->andWhere($qb->expr()->eq($as . '.name', ':name'));

        $parameters = ['name' => $name];

        if (0 < strpos($name, ':')) {
            [$rootName, $menuName] = explode(':', $name);

            /** @var MenuInterface $root */
            if (null === $root = $this->findOneBy(['name' => $rootName])) {
                throw new InvalidArgumentException(sprintf('Root menu "%s" not found.', $rootName));
            }

            $qb->andWhere($qb->expr()->eq($as . '.root', ':root'));

            $parameters = [
                'name' => $menuName,
                'root' => $root,
            ];
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        return $qb
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->enableResultCache(3600, Menu::getEntityTagPrefix() . '[name=' . $name . ']')
            //->setMaxResults(1)
            ->setParameters($parameters)
            ->getOneOrNullResult();
    }

    /**
     * Returns the menus data for the menu provider.
     *
     * @return array[]
     */
    public function findForProvider(): array
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->select(
                'm.id, IDENTITY(m.parent) as parent, m.name, m.route, m.parameters, m.root, ' .
                'm.attributes, m.options, t.title, t.path'
            )
            ->leftJoin('m.translations', 't', Expr\Join::WITH, $qb->expr()->eq('t.locale', ':locale'))
            ->andWhere($qb->expr()->eq('m.enabled', ':enabled'))
            ->orderBy('m.left', 'ASC')
            ->addGroupBy('m.id');

        return $qb
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->enableResultCache(3600, Menu::getEntityTagPrefix()) // TODO Use a more unique cache id and invalidate it.
            ->setParameters([
                'locale'  => $this->localeProvider->getCurrentLocale(),
                'enabled' => true,
            ])
            ->getArrayResult();
    }

    /**
     * Finds menu by route.
     *
     * @param string $route
     *
     * @return MenuInterface[]
     */
    public function findByRoute(string $route): array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb
            ->andWhere($qb->expr()->eq('m.route', ':route'))
            ->getQuery()
            ->setParameter('route', $route)
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    protected function getAlias(): string
    {
        return 'm';
    }
}
