<?php

namespace Ekyna\Bundle\CmsBundle\DataFixtures\ORM;

use Hautelook\AliceBundle\Alice\DataFixtureLoader as BaseLoader;
use Nelmio\Alice\Fixtures;

/**
 * Class AbstractFixture
 * @package Ekyna\Bundle\CmsBundle\DataFixtures\ORM
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
abstract class DataFixtureLoader extends BaseLoader
{
    /**
     * {@inheritdoc}
     */
    protected function getProcessors()
    {
        return array(
            new CmsProcessor($this->container),
        );
    }
}
