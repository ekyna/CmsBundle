<?php

namespace Ekyna\Bundle\CmsBundle\Tests\Editor\Adapter;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\Bootstrap3Adapter;
use Ekyna\Bundle\CmsBundle\Entity\Editor\Block;
use PHPUnit\Framework\TestCase;

/**
 * Class Bootstrap3AdapterTest
 * @package Ekyna\Bundle\CmsBundle\Tests\Editor\Adapter
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Bootstrap3AdapterTest extends TestCase
{
    /**
     * @var Bootstrap3Adapter
     */
    private $adapter;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->adapter = new Bootstrap3Adapter();

        // TODO inject editor mock (?)
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        $this->adapter = null;
    }

    /**
     * @param array $layout
     * @param array $expected
     *
     * @dataProvider imageFiltersMapData
     */
    public function testGetImageResponsiveMap($layout, $expected)
    {
        $block = $this->mockBlock($layout);

        $result = $this->adapter->getImageResponsiveMap($block);

        $this->assertEquals($expected, $result);
    }

    public function imageFiltersMapData()
    {
        // real-size: w
        // max-width: px
        // size: px

        return [
            [
                [
                    'sm' => ['size' => 12],
                    'lg' => ['size' => 6],
                ],
                [
                    'col-xs-12' => ['max' => 479, 'width' => 450],
                    'col-sm-12' => ['max' => 991, 'width' => 720],
                    'col-md-12' => ['max' => 1199, 'width' => 940],
                    'col-lg-6'  => ['max' => null, 'width' => 555],
                ],
            ],
            [
                [
                    'xs' => ['size' => 10],
                    'sm' => ['size' => 8],
                    'md' => ['size' => 6],
                    'lg' => ['size' => 4],
                ],
                [
                    'col-xs-10' => ['max' => 479, 'width' => 370],
                    'col-sm-8'  => ['max' => 991, 'width' => 470],
                    'col-md-6'  => ['max' => 1199, 'width' => 455],
                    'col-lg-4'  => ['max' => null, 'width' => 360],
                ],
            ],
        ];
    }

    /**
     * @param $data
     * @param $expected
     *
     * @dataProvider updateBlockData
     */
    public function testUpdateBlockLayout($data, $expected)
    {
        $block = $this->mockBlock();

        $this->adapter->updateBlockLayout($block, $data);

        $this->assertEquals($expected, $block->getLayout());
    }

    public function updateBlockData()
    {
        return [
            [
                [
                    'xs' => ['size' => 6],
                    'sm' => ['size' => 6],
                    'md' => ['size' => 6],
                    'lg' => ['size' => 6],
                ],
                [
                    'xs' => ['size' => 6],
                ],
            ],
            [
                [
                    'xs' => ['size' => 12],
                    'sm' => ['size' => 6],
                    'md' => ['size' => 6],
                    'lg' => ['size' => 6],
                ],
                [
                    'sm' => ['size' => 6],
                ],
            ],
            [
                [
                    'xs'             => ['size' => 6],
                    'sm'             => ['size' => 12],
                    'md'             => ['size' => 12],
                    'lg'             => ['size' => 6],
                    'padding_top'    => 0,
                    'padding_bottom' => 30,
                ],
                [
                    'xs'             => ['size' => 6],
                    'sm'             => ['size' => 12],
                    'lg'             => ['size' => 6],
                    'padding_bottom' => 30,
                ],
            ],
        ];
    }

    /**
     * Creates and returns a block.
     *
     * @param array $layout
     *
     * @return Block
     */
    private function mockBlock(array $layout = [])
    {
        $block = new Block();

        $block->setLayout($layout);

        return $block;
    }
}
