<?php

namespace Ekyna\Bundle\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Hautelook\AliceBundle\Alice\DataFixtureLoader as Loader;
use Nelmio\Alice\Fixtures;

/**
 * Class LoadFixtureData
 * @package Ekyna\Bundle\CmsBundle\DataFixtures\ORM
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class LoadFixtureData extends Loader implements OrderedFixtureInterface
{
    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return array();

        //return [__DIR__.'/fixtures.yml'];
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
