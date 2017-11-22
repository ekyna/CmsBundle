<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Adapter;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidArgumentException;
use Ekyna\Bundle\CmsBundle\Editor\Exception\RuntimeException;
use Ekyna\Bundle\CmsBundle\Editor\View;
use Ekyna\Bundle\CmsBundle\Editor\Model;

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
            static::LG,
        ];
        foreach (array_diff(array_keys($data), $expectedKeys) as $property) {
            unset($data[$property]);
        }

        $this->validateLayoutStyles($data);
        $this->validateBlockLayout($data);

        $data = $this->cleanUpBlockLayout($block, $data);

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
     * @param Model\BlockInterface $block
     * @param array                $data
     *
     * @return array
     */
    protected function cleanUpBlockLayout(Model\BlockInterface $block, array $data)
    {
        $clean = [];

        // TODO responsive padding
        if (isset($data[static::PADDING_TOP])) {
            $clean[static::PADDING_TOP] = $data[static::PADDING_TOP];
        }
        if (isset($data[static::PADDING_BOTTOM])) {
            $clean[static::PADDING_BOTTOM] = $data[static::PADDING_BOTTOM];
        }

        $hasPreviousSize = $hasPreviousOffset = false;
        $previousSize = $previousOffset = null;

        /** @var Model\BlockInterface[] $blocks */
        $blocks = [];
        if (null !== $row = $block->getRow()) {
            $blocks = $row->getBlocks()->filter(function(Model\BlockInterface $b) use ($block) {
                return $b !== $block;
            });
        }

        $devices = array_reverse(array_keys(static::getDevicesWidths()));

        foreach ($devices as $device) {
            // Do we need to clear the previous block layout (which size is lower than 12)
            $clear = false;
            foreach ($blocks as $b) {
                $d = $b->getLayout();
                if (isset($d[$device][static::SIZE]) && 12 > $d[$device][static::SIZE]) {
                    $clear = true;
                    break;
                }
            }

            // If layout is set for this device
            if (!$clear && !isset($data[$device])) {
                continue;
            }

            $size = isset($data[$device][static::SIZE]) ? $data[$device][static::SIZE] : $previousSize;
            $offset = isset($data[$device][static::OFFSET]) ? $data[$device][static::OFFSET] : $previousOffset;

            $cleanDevice = [];

            if ($clear || ($hasPreviousSize && $previousSize != $size) || (!$hasPreviousSize && 12 > $size)) {
                $cleanDevice[static::SIZE] = $size;
            }
            if (($hasPreviousOffset && $previousOffset != $offset) || (!$hasPreviousSize && 0 < $offset)) {
                $cleanDevice[static::OFFSET] = $offset;
            }

            if (!$hasPreviousSize && 12 > $size) {
                $hasPreviousSize = true;
            }
            if (!$hasPreviousOffset && 0 < $offset) {
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
            && (0 > $layout[static::PADDING_TOP] || 300 < $layout[static::PADDING_TOP])
        ) {
            throw new InvalidArgumentException('Invalid layout padding top');
        }

        if (isset($layout[static::PADDING_BOTTOM])
            && (0 > $layout[static::PADDING_BOTTOM] || 300 < $layout[static::PADDING_BOTTOM])
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
