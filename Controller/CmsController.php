<?php

namespace Ekyna\Bundle\CmsBundle\Controller;

use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CmsController
 * @package Ekyna\Bundle\CmsBundle\Controller
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsController extends Controller
{
    public function defaultAction(Request $request)
    {
        return $this->render($this->container->getParameter('ekyna_cms.default_template'));
    }
}
