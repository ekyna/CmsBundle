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
    /**
     * Default action.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function defaultAction(Request $request)
    {
        return $this
            ->render('EkynaCmsBundle:Cms:default.html.twig')
            ->setSharedMaxAge($this->container->getParameter('ekyna_cms.default_max_age'))
        ;
    }

    /**
     * Menu action.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuAction(array $options = array('style' => 'navbar'))
    {
        return $this
            ->render('EkynaCmsBundle:Cms:menu.html.twig', array('options' => $options))
            ->setPublic()
            ->setMaxAge(3600)
            ->setSharedMaxAge(3600)
        ;
    }
}
