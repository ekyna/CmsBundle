<?php

namespace Ekyna\Bundle\CmsBundle\Menu;

use Ekyna\Bundle\CmsBundle\Event\MenuEvent;
use Ekyna\Bundle\CmsBundle\Repository\MenuRepository;
use Ekyna\Component\Resource\Locale\LocaleProviderInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class MenuProvider
 * @package Ekyna\Bundle\CmsBundle\Menu
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuProvider implements MenuProviderInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var FactoryInterface
     */
    protected $factory;

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
     * @param EventDispatcherInterface $dispatcher
     * @param FactoryInterface         $factory
     * @param MenuRepository           $menuRepository
     * @param LocaleProviderInterface  $localeProvider
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        FactoryInterface $factory,
        MenuRepository $menuRepository,
        LocaleProviderInterface $localeProvider
    ) {
        $this->dispatcher = $dispatcher;
        $this->factory = $factory;
        $this->menuRepository = $menuRepository;
        $this->localeProvider = $localeProvider;
    }

    /**
     * Checks whether a menu exists in this provider
     *
     * @param string $name
     * @param array  $options
     *
     * @return bool
     */
    public function has($name, array $options = [])
    {
        return null !== $this->findByName($name);
    }

    /**
     * Finds the menu by his name.
     *
     * @param string $name
     *
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
     * @param array  $options
     *
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException
     */
    public function get($name, array $options = [])
    {
        if (null === $menu = $this->findByName($name)) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        return $this->buildItem($menu, array_merge([
            'attributes' => ['id' => $menu['name'] . '-nav'] // Root css id
        ], $options));
    }

    /**
     * Builds the menu item.
     *
     * @param array $data
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    private function buildItem(array $data, array $options = [])
    {
        $options = array_merge($options, [
            'label' => $data['title'],
        ]);
        if (!empty($data['attributes'])) {
            $options['attributes'] = $data['attributes'];
        }

        // Fix routing / url / path
        if (0 < strlen($data['path'])) {
            $options['uri'] = $data['path'];
        } elseif (0 < strlen($data['route'])) {
            $options['route'] = $data['route'];
            if (!empty($data['parameters'])) {
                $options['routeParameters'] = $data['parameters'];
            }
        }

        $item = $this
            ->factory
            ->createItem($data['name'], $options)
            ->setExtra('translation_domain', false);

        // Children items
        foreach ($this->menus as $menu) {
            if ($data['id'] === intval($menu['parent'])) {
                $item->addChild($this->buildItem($menu));
            }
        }

        // Custom menu event
        if (isset($data['options']['event']) && 0 < strlen($event = $data['options']['event'])) {
            $this->dispatcher->dispatch($event, new MenuEvent($this->factory, $item));
        }

        return $item;
    }

    /**
     * Loads the menus.
     */
    private function loadMenus()
    {
        if (null === $this->menus) {
            $this->menus = $this->menuRepository->findForProvider();
        }
    }
}
