<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Adapter;

use Ekyna\Bundle\CmsBundle\Editor\EditorAwareTrait;

/**
 * Class AbstractAdapter
 * @package Ekyna\Bundle\CmsBundle\Editor\Adapter
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractAdapter implements AdapterInterface
{
    use EditorAwareTrait;
}
