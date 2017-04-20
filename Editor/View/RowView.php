<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class RowView
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class RowView extends AbstractView
{
    /** @var BlockView[] */
    public array $blocks = [];
}
