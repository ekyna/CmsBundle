<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Adapter;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidArgumentException;
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

        $this->applyLayoutStyles($view->getAttributes(), $container->getLayout());
    }

    /**
     * @inheritdoc
     */
    public function buildRow(Model\RowInterface $row, View\RowView $view)
    {
        $view->getAttributes()->addClass('row');

        $this->applyLayoutStyles($view->getAttributes(), $row->getLayout());
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

        $this->applyLayoutStyles($attributes, $layout);

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
     * @inheritDoc
     */
    public function updateContainerLayout(Model\ContainerInterface $container, array $data)
    {
        foreach (array_diff_key($data, [static::PADDING_TOP, static::PADDING_BOTTOM]) as $property) {
            unset($data[$property]);
        }

        $this->validateLayoutStyles($data);

        $container->setLayout($data);
    }

    /**
     * @inheritDoc
     */
    public function updateRowLayout(Model\RowInterface $row, array $data)
    {
        foreach (array_diff_key($data, [static::PADDING_TOP, static::PADDING_BOTTOM]) as $property) {
            unset($data[$property]);
        }

        $this->validateLayoutStyles($data);

        $row->setLayout($data);
    }

    /**
     * @inheritDoc
     */
    public function updateBlockLayout(Model\BlockInterface $block, array $data)
    {
        $expectedKeys = [
            static::PADDING_TOP,
            static::PADDING_BOTTOM,
            static::XS,
            static::SM,
            static::MD,
            static::LG
        ];
        foreach (array_diff(array_keys($data), $expectedKeys) as $property) {
            unset($data[$property]);
        }

        $this->validateLayoutStyles($data);
        $this->validateBlockLayout($data);

        $data = $this->cleanUpBlockLayout($data);

        $block->setLayout($data);
    }

    /**
     * Validates the block layout.
     *
     * @param array $data
     *
     * @throws InvalidArgumentException
     */
    protected function validateBlockLayout(array $data)
    {
        foreach (array_keys(static::getDevicesWidths()) as $device) {
            // If layout set for this device
            if (!isset($data[$device])) {
                continue;
            }

            $size = isset($data[$device][static::SIZE]) ? $data[$device][static::SIZE] : 12;
            $offset = isset($data[$device][static::OFFSET]) ? $data[$device][static::OFFSET] : 0;

            // Validate size
            if (1 > $size || $size > 12) {
                throw new InvalidArgumentException('Invalid layout size');
            }

            // Validate offset
            if (0 > $offset || $offset > 11) {
                throw new InvalidArgumentException('Invalid layout offset');
            }

            // Validate sum
            if (12 < ($size + $offset)) {
                throw new InvalidArgumentException('Invalid block layout size/offset');
            }
        }
    }

    /**
     * Cleans up the block layout.
     *
     * @param array $data
     *
     * @return array
     */
    protected function cleanUpBlockLayout(array $data)
    {
        $clean = [];

        $hasPreviousSize = $hasPreviousOffset = false;
        $previousSize = $previousOffset = null;

        foreach (array_keys(static::getDevicesWidths()) as $device) {
            // If layout is set for this device
            if (!isset($data[$device])) {
                continue;
            }

            $size = isset($data[$device][static::SIZE]) ? $data[$device][static::SIZE] : $previousSize;
            $offset = isset($data[$device][static::OFFSET]) ? $data[$device][static::OFFSET] : $previousOffset;

            $cleanDevice = [];

            if (($hasPreviousSize && 12 === $size) || 12 > $size) {
                $cleanDevice[static::SIZE] = $size;
            }
            if (($hasPreviousOffset && 0 === $offset) || 0 < $offset) {
                $cleanDevice[static::OFFSET] = $offset;
            }

            if (12 > $size) {
                $hasPreviousSize = true;
            }
            if (0 < $offset) {
                $hasPreviousOffset = true;
            }

            $previousSize = $size;
            $previousOffset = $offset;

            if (!empty($cleanDevice)) {
                $clean[$device] = $cleanDevice;
            }
        }

        return $clean;
    }

    /**
     * Validates the layout styles.
     *
     * @param array $layout
     */
    protected function validateLayoutStyles(array $layout)
    {
        if (isset($layout[static::PADDING_TOP])
            && (0 > $layout[static::PADDING_TOP] || 100 < $layout[static::PADDING_TOP])
        ) {
            throw new InvalidArgumentException('Invalid layout padding top');
        }

        if (isset($layout[static::PADDING_BOTTOM])
            && (0 > $layout[static::PADDING_BOTTOM] || 100 < $layout[static::PADDING_BOTTOM])
        ) {
            throw new InvalidArgumentException('Invalid layout padding bottom');
        }
    }

    /**
     * Adds the css styles regarding to the layout data.
     *
     * @param View\AttributesInterface $attributes
     * @param array                    $layout
     */
    protected function applyLayoutStyles(View\AttributesInterface $attributes, array $layout)
    {
        foreach ([static::PADDING_TOP => '%spx', static::PADDING_BOTTOM => '%spx'] as $property => $template) {
            if (isset($layout[$property]) && 0 < $layout[$property]) {
                $attributes->addStyle(
                    str_replace('_', '-', $property),
                    sprintf($template, $layout[$property])
                );
            }
        }
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
