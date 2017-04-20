<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginInterface as BaseInterface;
use Ekyna\Bundle\CmsBundle\Editor\View\BlockView;
use Ekyna\Bundle\CmsBundle\Editor\View\WidgetView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Interface PluginInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface PluginInterface extends BaseInterface
{
    /**
     * Creates a new block.
     *
     * @param BlockInterface $block
     * @param array          $data
     */
    public function create(BlockInterface $block, array $data = []): void;

    /**
     * Updates a block.
     *
     * @param BlockInterface $block
     * @param Request        $request
     * @param array          $options
     *
     * @return Response|null
     *
     * @throws EditorExceptionInterface
     */
    public function update(BlockInterface $block, Request $request, array $options = []): ?Response;

    /**
     * Removes a block.
     *
     * @param BlockInterface $block
     */
    public function remove(BlockInterface $block): void;

    /**
     * Validates the block (data).
     *
     * @param BlockInterface            $block
     * @param ExecutionContextInterface $context
     */
    public function validate(BlockInterface $block, ExecutionContextInterface $context): void;

    /**
     * Returns the block content.
     *
     * @param BlockInterface   $block
     * @param BlockView        $view
     * @param AdapterInterface $adapter
     * @param array            $options
     */
    public function render(BlockInterface $block, BlockView $view, AdapterInterface $adapter, array $options): void;

    /**
     * Creates the widget view.
     *
     * @param BlockInterface   $block
     * @param AdapterInterface $adapter
     * @param array            $options
     * @param int              $position
     *
     * @return WidgetView
     */
    public function createWidget(
        BlockInterface $block,
        AdapterInterface $adapter,
        array $options,
        int $position = 0
    ): WidgetView;

    /**
     * Returns whether the block is supported.
     *
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function supports(BlockInterface $block): bool;
}
