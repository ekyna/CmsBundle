<?php

namespace Ekyna\Bundle\CmsBundle\Menu;

use Ekyna\Bundle\CmsBundle\Entity\MenuRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\Loader\NodeLoader;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var RequestStack
     */
    protected $requestStack;


    /**
     * Constructor.
     *
     * @param FactoryInterface $factory
     * @param MenuRepository $menuRepository
     * @param RequestStack $requestStack
     */
    public function __construct(
        FactoryInterface $factory,
        MenuRepository   $menuRepository,
        RequestStack     $requestStack
    ) {
        $this->factory = $factory;
        $this->menuRepository = $menuRepository;
        $this->requestStack = $requestStack;
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
        /** @var \Ekyna\Bundle\CmsBundle\Entity\Menu $menu  */
        if (null === $menu = $this->menuRepository->findOneByName($name)) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        $menu->addOptions($options);
        $loader = new NodeLoader($this->factory);

        return $loader->load($menu);
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
        $menu = $this->menuRepository->findOneByName($name);

        return $menu !== null;
    }
}
