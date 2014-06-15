<?php

namespace Ekyna\Bundle\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * CmsController.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsController extends Controller
{
    public function defaultAction(Request $request)
    {
        return $this->render($this->container->getParameter('ekyna_cms.default_template'));
    }
}
