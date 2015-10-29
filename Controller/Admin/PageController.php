<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Admin;

use Ekyna\Bundle\AdminBundle\Controller\Resource;
use Ekyna\Bundle\AdminBundle\Controller\ResourceController;

/**
 * Class PageController
 * @package Ekyna\Bundle\CmsBundle\Controller\Admin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageController extends ResourceController
{
    use Resource\NestedTrait,
        Resource\TinymceTrait,
        Resource\ToggleableTrait;
}
