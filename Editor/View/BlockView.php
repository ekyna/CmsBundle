<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class BlockView
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BlockView extends AbstractView
{
    /** @var WidgetView[] */
    public array $widgets = [];
}
