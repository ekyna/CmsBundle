<?php

namespace Ekyna\Bundle\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * CmsController
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsController extends Controller
{
    public function defaultAction()
    {
        return $this->render($this->container->getParameter('ekyna_cms.default_template'));
    }
}
