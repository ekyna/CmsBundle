<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Admin;

use Ekyna\Bundle\AdminBundle\Controller\Resource\ToggleableTrait;
use Ekyna\Bundle\AdminBundle\Controller\ResourceController;
use Ekyna\Bundle\AdminBundle\Controller\Resource\NestedTrait;
use Ekyna\Bundle\AdminBundle\Controller\Resource\TinymceTrait;
use Ekyna\Bundle\AdminBundle\Controller\Context;

/**
 * Class PageController
 * @package Ekyna\Bundle\CmsBundle\Controller\Admin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageController extends ResourceController
{
    use NestedTrait {
        createNewFromParent as traitCreateNewFromParent;
    }
    use TinymceTrait;
    use ToggleableTrait;

    /**
     * {@inheritDoc}
     */
    /*public function createNew(Context $context)
    {
        $resource = parent::createNew($context);
        $resource->setController($this->container->getParameter('ekyna_cms.default_controller'));

        return $resource;
    }*/

    /**
     * {@inheritDoc}
     */
    public function createNewFromParent(Context $context, $parent)
    {
        /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $parent */
        $resource = $this->traitCreateNewFromParent($context, $parent);
        //$resource->setController($this->container->getParameter('ekyna_cms.default_controller'));
        if ($parent->getAdvanced()) {
            $resource->setAdvanced(true);
        }

        return $resource;
    }
}
