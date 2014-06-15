<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Admin;

use Ekyna\Bundle\AdminBundle\Controller\ResourceController;
use Ekyna\Bundle\AdminBundle\Controller\Resource\NestedTrait;
use Ekyna\Bundle\AdminBundle\Controller\Resource\TinymceTrait;

/**
 * PageController
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageController extends ResourceController
{
    use NestedTrait;
    use TinymceTrait;
}
