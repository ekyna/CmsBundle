<?php

namespace Ekyna\Bundle\CmsBundle\Menu;

use Doctrine\ORM\Query\Expr;
use Ekyna\Bundle\CmsBundle\Entity\MenuRepository;
use Ekyna\Bundle\CoreBundle\Locale\LocaleProviderInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;

/**
 * Class MenuProvider
 * @package Ekyna\Bundle\CmsBundle\Menu
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuProvider implements MenuProviderInterface
{
    /**
     * @var FactoryInterface
     */
    protected $factory = null;

    /**
     * @var MenuRepository
     */
    protected $menuRepository;

    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @var array
     */
    protected $menus;


    /**
     * Constructor.
     *
     * @param FactoryInterface        $factory
     * @param MenuRepository          $menuRepository
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(
        FactoryInterface        $factory,
        MenuRepository          $menuRepository,
        LocaleProviderInterface $localeProvider
    ) {
        $this->factory        = $factory;
        $this->menuRepository = $menuRepository;
        $this->localeProvider = $localeProvider;
    }

    /**
     * Checks whether a menu exists in this provider
     *
     * @param string $name
     * @param array $options
     * @return bool
     */
    public function has($name, array $options = array())
    {
        return null !== $this->findByName($name);
    }

    /**
     * Finds the menu by his name.
     *
     * @param $name
     * @return null
     */
    public function findByName($name)
    {
        $this->loadMenus();

        $rootId = 0;
        if (0 < strpos($name, ':')) {
            list($rootName, $name) = explode(':', $name);
            if (null === $root = $this->findByName($rootName)) {
                throw new \InvalidArgumentException(sprintf('Root menu "%s" not found.', $rootName));
            }
            $rootId = intval($root['id']);
        }

        foreach ($this->menus as $menu) {
            if ($menu['name'] === $name && !(0 < $rootId && intval($menu['root']) != $rootId)) {
                return $menu;
            }
        }

        return null;
    }

    /**
     * Retrieves a menu by its name.
     *
     * @param string $name
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException
     */
    public function get($name, array $options = array())
    {
        if (null === $menu = $this->findByName($name)) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        return $this->buildItem($menu, array_merge(array(
            'attributes' => array('id' => $menu['name'].'-nav') // Root css id
        ), $options));
    }

    /**
     * Builds the menu item.
     *
     * @param array $data
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    private function buildItem(array $data, array $options = array())
    {
        $options = array_merge($options, array(
            'label' => $data['title'],
        ));
        if (!empty($data['attributes'])) {
            $options['attributes'] = $data['attributes'];
        }
        if (0 < strlen($data['path'])) {
            $options['uri'] = $data['path'];
        } elseif (0 < strlen($data['route'])) {
            $options['route'] = $data['route'];
            if (!empty($data['parameters'])) {
                $options['routeParameters'] = $data['parameters'];
            }
        }

        $item = $this->factory->createItem($data['name'], $options);

        foreach ($this->menus as $menu) {
            if ($data['id'] === intval($menu['parent'])) {
                $item->addChild($this->buildItem($menu));
            }
        }

        return $item;
    }

    /**
     * Loads the menus.
     */
    private function loadMenus()
    {
        if (null === $this->menus) {
            $qb = $this->menuRepository->createQueryBuilder('m');
            $qb
                ->select('m.id, IDENTITY(m.parent) as parent, m.name, m.route, m.parameters, m.attributes, m.root, t.title, t.path')
                ->join('m.translations', 't', Expr\Join::WITH, $qb->expr()->eq('t.locale',
                    $qb->expr()->literal($this->localeProvider->getCurrentLocale())
                ))
                ->orderBy('m.left', 'asc')
            ;
            $this->menus = $qb->getQuery()->getArrayResult();
        }
    }
}
