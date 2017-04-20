<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Renderer;

use Ekyna\Bundle\CmsBundle\Entity\Menu;
use Ekyna\Bundle\CmsBundle\Service\Menu\MenuProvider;
use Ekyna\Bundle\ResourceBundle\Service\Http\TagManager;
use InvalidArgumentException;
use Knp\Menu\Twig\Helper;

use function sprintf;

/**
 * Class MenuRenderer
 * @package Ekyna\Bundle\CmsBundle\Service\Renderer
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuRenderer
{
    protected MenuProvider $menuProvider;
    protected Helper       $menuHelper;
    protected TagManager   $tagManager;

    public function __construct(
        MenuProvider $menuProvider,
        Helper       $menuHelper,
        TagManager   $tagManager
    ) {
        $this->menuProvider = $menuProvider;
        $this->menuHelper = $menuHelper;
        $this->tagManager = $tagManager;
    }

    /**
     * Renders the menu by his name.
     */
    public function renderMenu(string $name, array $options = [], string $renderer = null): string
    {
        if (null === $menu = $this->menuProvider->findByName($name)) {
            throw new InvalidArgumentException(sprintf('Menu named "%s" not found.', $name));
        }

        // Tags the response as Menu relative
        $this->tagManager->addTags([
            Menu::getEntityTagPrefix(),
            sprintf('%s[id:%s]', Menu::getEntityTagPrefix(), $menu['id']),
        ]);

        return $this->menuHelper->render($name, $options, $renderer);
    }

    /**
     * Renders the breadcrumb.
     */
    public function renderBreadcrumb(array $options = []): string
    {
        $options = array_replace([
            'template' => '@EkynaCms/Cms/breadcrumb.html.twig',
            //'currentAsLink' => false,
            'depth'    => 1,
        ], $options);

        return $this->menuHelper->render('ekyna_cms.breadcrumb', $options);
    }
}
