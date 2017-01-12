<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Adapter;

use Ekyna\Bundle\CmsBundle\Editor\Exception\RuntimeException;
use Ekyna\Bundle\CmsBundle\Editor\View;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Class Bootstrap3Adapter
 * @package Ekyna\Bundle\CmsBundle\Editor\Adapter
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Bootstrap3Adapter extends AbstractAdapter implements AdapterInterface
{
    const XS = 'xs';
    const SM = 'sm';
    const MD = 'md';
    const LG = 'lg';

    const SIZE   = 'size';
    const ORDER  = 'order';
    const OFFSET = 'offset';


    /**
     * @inheritdoc
     */
    public function buildContent(Model\ContentInterface $content, View\ContentView $view)
    {
        $view->getAttributes()->addClass('content');
    }

    /**
     * @inheritdoc
     */
    public function buildContainer(Model\ContainerInterface $container, View\ContainerView $view)
    {
        $view->getInnerAttributes()->addClass('container');
    }

    /**
     * @inheritdoc
     */
    public function buildRow(Model\RowInterface $row, View\RowView $view)
    {
        $view->getAttributes()->addClass('row');
    }

    /**
     * @inheritdoc
     */
    public function buildBlock(Model\BlockInterface $block, View\BlockView $view)
    {
        $attributes = $view->getAttributes();

        /**
         * $layout = [(string)][
         *     'size'   => (int),
         *     'offset' => (int),
         *     'order'  => (int),
         * ]
         */
        $layout = $block->getLayout();

        // CSS classes
        $classes = [];
        foreach ($layout as $device => $config) {
            // Size
            if (isset($config[static::SIZE])) {
                $classes[] = sprintf('col-%s-%d', $device, $config[static::SIZE]);
            }
            // Offset
            if (isset($config[static::OFFSET])) {
                $classes[] = sprintf('col-%s-offset-%d', $device, $config[static::OFFSET]);
            }
            // Order
            if (isset($config[static::ORDER])) {
                if (0 < $config[static::ORDER]) {
                    $classes[] = sprintf('col-%s-push-%d', $device, $config[static::ORDER]);
                } else {
                    $classes[] = sprintf('col-%s-pull-%d', $device, $config[static::ORDER]);
                }
            }
        }
        if (empty($classes)) {
            $classes[] = 'col-md-12';
        }

        // Editor data
        if ($this->editor->isEnabled()) {
            $attributes->setData([
                'actions' => [ // TODO
                    'offset_left'  => true,
                    'offset_right' => true,
                    'push'         => true,
                    'pull'         => true,
                    'expand'       => true,
                    'compress'     => true,
                    'add'          => true,
                    'remove'       => true,
                ],
            ]);
        }
        $attributes->addClass($classes);
    }

    /**
     * Creates the initial block layout regarding to given data.
     *
     * $data = [
     *   'layout' => [
     *      'size'   => (int),
     *      'order'  => (int),
     *      'offset' => (int),
     *   ]
     * ]
     */
    /*public function createBlock(Model\BlockInterface $block, array $data)
    {

    }*/

    /**
     * @inheritdoc
     */
    public function pullBlock(Model\BlockInterface $block)
    {
        throw new \Exception('Not yet implemented'); // TODO
    }

    /**
     * @inheritdoc
     */
    public function pushBlock(Model\BlockInterface $block)
    {
        throw new \Exception('Not yet implemented'); // TODO
    }

    /**
     * @inheritdoc
     */
    public function offsetLeftBlock(Model\BlockInterface $block)
    {
        $offset = $this->getCurrentBlockProperty($block, static::OFFSET, 0);
        $size = $this->getCurrentBlockProperty($block, static::SIZE, 12);

        // Decrement the offset if allowed
        if (0 < $offset && 1 < ($offset + $size)) {
            $offset--;
        }

        $this->setCurrentBlockProperty($block, static::OFFSET, $offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetRightBlock(Model\BlockInterface $block)
    {
        $offset = $this->getCurrentBlockProperty($block, static::OFFSET, 0);
        $size = $this->getCurrentBlockProperty($block, static::SIZE, 12);

        // Increment the offset if allowed
        if (12 > ($offset + $size)) {
            $offset++;
        }

        $this->setCurrentBlockProperty($block, static::OFFSET, $offset);
    }

    /**
     * @inheritdoc
     */
    public function compressBlock(Model\BlockInterface $block)
    {
        $size = $this->getCurrentBlockProperty($block, static::SIZE, 12);
        $offset = $this->getCurrentBlockProperty($block, static::OFFSET, 0);

        // Decrement the size if allowed
        if (1 < ($offset + $size)) {
            $size--;
        }

        $this->setCurrentBlockProperty($block, static::SIZE, $size);
    }

    /**
     * @inheritdoc
     */
    public function expandBlock(Model\BlockInterface $block)
    {
        $size = $this->getCurrentBlockProperty($block, static::SIZE, 12);
        $offset = $this->getCurrentBlockProperty($block, static::OFFSET, 0);

        // Increment the size if allowed
        if (12 > ($offset + $size)) {
            $size++;
        }

        $this->setCurrentBlockProperty($block, static::SIZE, $size);
    }

    /**
     * Returns the current block layout property.
     *
     * @param Model\BlockInterface $block
     * @param string               $property
     * @param int                  $default
     *
     * @return int
     */
    public function getCurrentBlockProperty(Model\BlockInterface $block, $property, $default)
    {
        $layout = $block->getLayout();
        $currentSize = $default;

        foreach ($this->resolveLowerDevices() as $d) {
            if (isset($layout[$d]) && isset($layout[$d][$property])) {
                $currentSize = $layout[$d][$property];
                break;
            }
        }

        return $currentSize;
    }

    /**
     * Cleans up the block layout property.
     *
     * @param Model\BlockInterface $block
     * @param string               $property
     * @param int                  $current
     */
    public function setCurrentBlockProperty(Model\BlockInterface $block, $property, $current)
    {
        $layout = $block->getLayout();

        // Update the current device layout
        $currentDevice = $this->resolveCurrentDevice();
        $layout = array_replace_recursive($layout, [
            $currentDevice => [
                $property => $current,
            ],
        ]);

        // Clear the property for upper devices layouts if equals
        foreach ($this->resolveGreaterDevices() as $d) {
            if (isset($layout[$d]) && isset($layout[$d][$property]) && $layout[$d][$property] == $current) {
                unset($layout[$d][$property]);
            }
        }

        $block->setLayout($layout);
    }

    /**
     * Resolves the current device constant regarding to the editor's viewport width.
     *
     * @return string
     * @throws RuntimeException
     */
    private function resolveCurrentDevice()
    {
        if (0 == $viewportWidth = $this->editor->getViewportWidth()) {
            throw new RuntimeException('Unexpected editor viewport width.');
        }

        foreach ($this->getDevicesWidths() as $device => $deviceWidth) {
            if ($deviceWidth <= $viewportWidth) {
                return $device;
            }
        }

        return static::XS;
    }

    /**
     * Resolves the devices constants lower than (or equal to) the editor's viewport width.
     *
     * @return array
     * @throws RuntimeException
     */
    private function resolveLowerDevices()
    {
        if (0 == $viewportWidth = $this->editor->getViewportWidth()) {
            throw new RuntimeException('Unexpected editor viewport width.');
        }

        $devices = [];

        foreach ($this->getDevicesWidths() as $device => $deviceWidth) {
            if ($viewportWidth >= $deviceWidth) {
                $devices[] = $device;
            }
        }

        if (empty($devices)) {
            $devices[] = static::XS;
        }

        return $devices;
    }

    /**
     * Resolves the devices constants greater than the editor's viewport width.
     *
     * @return array
     * @throws RuntimeException
     */
    private function resolveGreaterDevices()
    {
        if (0 == $viewportWidth = $this->editor->getViewportWidth()) {
            throw new RuntimeException('Unexpected editor viewport width.');
        }

        $devices = [];

        foreach ($this->getDevicesWidths() as $device => $deviceWidth) {
            if ($viewportWidth < $deviceWidth) {
                $devices[] = $device;
            }
        }

        return array_reverse($devices);
    }

    /**
     * Returns the devices min widths.
     *
     * @return array
     */
    private function getDevicesWidths()
    {
        return [
            static::LG => 1200,
            static::MD => 992,
            static::SM => 768,
            static::XS => 0,
        ];
    }
}
