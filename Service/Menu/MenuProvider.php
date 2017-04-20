<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Menu;

use Ekyna\Bundle\CmsBundle\Event\MenuEvent;
use Ekyna\Bundle\CmsBundle\Repository\MenuRepositoryInterface;
use Ekyna\Component\Resource\Locale\LocaleProviderInterface;
use InvalidArgumentException;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class MenuProvider
 * @package Ekyna\Bundle\CmsBundle\Service\Menu
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuProvider implements MenuProviderInterface
{
    protected EventDispatcherInterface $dispatcher;
    protected FactoryInterface         $factory;
    protected MenuRepositoryInterface  $menuRepository;
    protected LocaleProviderInterface  $localeProvider;
    protected ?array                   $menus = null;


    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param FactoryInterface         $factory
     * @param MenuRepositoryInterface  $menuRepository
     * @param LocaleProviderInterface  $localeProvider
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        FactoryInterface $factory,
        MenuRepositoryInterface $menuRepository,
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
    public function has(string $name, array $options = []): bool
    {
        return null !== $this->findByName($name);
    }

    /**
     * Finds the menu by his name.
     *
     * @param string $name
     *
     * @return array|null
     */
    public function findByName(string $name): ?array
    {
        $this->loadMenus();

        $rootId = 0;
        if (0 < strpos($name, ':')) {
            [$rootName, $name] = explode(':', $name);
            if (null === $root = $this->findByName($rootName)) {
                throw new InvalidArgumentException(sprintf('Root menu "%s" not found.', $rootName));
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
     * @return ItemInterface
     * @throws InvalidArgumentException
     */
    public function get(string $name, array $options = []): ItemInterface
    {
        if (null === $menu = $this->findByName($name)) {
            throw new InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
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
     * @return ItemInterface
     */
    private function buildItem(array $data, array $options = []): ItemInterface
    {
        $options = array_merge($options, [
            'label' => $data['title'],
        ]);
        if (!empty($data['attributes'])) {
            $options['attributes'] = $data['attributes'];
        }

        // Fix routing / url / path
        if (!empty($data['path'])) {
            $options['uri'] = $data['path'];
        } elseif (!empty($data['route'])) {
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
        if (isset($data['options']['event']) && !empty($event = $data['options']['event'])) {
            $this->dispatcher->dispatch(new MenuEvent($this->factory, $item), $event);
        }

        return $item;
    }

    /**
     * Loads the menus.
     */
    private function loadMenus(): void
    {
        if ($this->menus) {
            return;
        }

        $this->menus = $this->menuRepository->findForProvider();
    }
}
