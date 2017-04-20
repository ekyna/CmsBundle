<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Ekyna\Bundle\UiBundle\Model\Modal;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlockController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BlockController extends AbstractController
{
    /**
     * Edits the block.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Request $request): Response
    {
        $block = $this->findBlockByRequest($request);

        try {
            $response = $this->editor->getBlockManager()->update($block, $request);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        if ($response instanceof Modal) {
            return $this->renderModal($response);
        } elseif ($response instanceof Response) {
            return $response;
        }

        $this->validate($block);
        $this->persist($block);

        $view = $this->getViewBuilder()->buildBlock($block);

        if (!empty($block->getName())) {
            $data = ['widgets' => $view->widgets];
        } else {
            $data = ['blocks' => [$view]];
        }

        return $this->buildResponse($data, self::SERIALIZE_CONTENT);
    }

    /**
     * Updates the block layout.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function layout(Request $request): Response
    {
        $block = $this->findBlockByRequest($request);

        $data = $request->request->get('data', []);

        try {
            $this->editor->getLayoutAdapter()->updateBlockLayout($block, $data);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        $this->validate($block);
        $this->persist($block);

        $data = [
            'blocks' => [$this->getViewBuilder()->buildBlock($block)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Changes the block type.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function changeType(Request $request): Response
    {
        $block = $this->findBlockByRequest($request);
        $type = $request->request->get('type');
        $removed = [];

        if ($type != $block->getType()) {
            foreach ($this->getViewBuilder()->buildBlock($block)->widgets as $widget) {
                $removed[] = $widget->getAttributes()->getId();
            }

            try {
                $this->editor->getBlockManager()->changeType($block, $type);
            } catch (EditorExceptionInterface $e) {
                return $this->handleException($e);
            }

            $this->validate($block);
            $this->persist($block);
        }

        $data = [
            'removed' => $removed,
            'blocks'  => [
                $this->getViewBuilder()->buildBlock($block),
            ],
        ];

        return $this->buildResponse($data, self::SERIALIZE_CONTENT);
    }

    /**
     * Removes the block.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function remove(Request $request): Response
    {
        $block = $this->findBlockByRequest($request);
        $row = $block->getRow();

        try {
            $this->editor->getBlockManager()->delete($block);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        // Stores id for front removal
        $removedId = $this->getViewBuilder()->buildBlock($block)->getAttributes()->getId();

        $this->validate($row);
        $this->persist($row);

        $data = [
            'removed' => [$removedId],
            //'rows'    => [$this->getViewBuilder()->buildRow($row)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Moves the block up.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function moveUp(Request $request): Response
    {
        $block = $this->findBlockByRequest($request);
        $row = $block->getRow();

        $removedId = $this->getViewBuilder()->buildBlock($block)->getAttributes()->getId();

        try {
            $sibling = $this->editor->getBlockManager()->moveUp($block);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        $this->validate($row);
        $this->validate($sibling);
        $this->persist($row);
        $this->persist($sibling);

        $createdId = $this->getViewBuilder()->buildBlock($block)->getAttributes()->getId();

        $data = [
            'removed' => [$removedId],
            'created' => $createdId,
            'rows'    => [
                $this->getViewBuilder()->buildRow($row),
                $this->getViewBuilder()->buildRow($sibling),
            ],
        ];

        return $this->buildResponse($data, self::SERIALIZE_FULL);
    }

    /**
     * Moves the block down.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function moveDown(Request $request): Response
    {
        $block = $this->findBlockByRequest($request);
        $row = $block->getRow();

        $removedId = $this->getViewBuilder()->buildBlock($block)->getAttributes()->getId();

        try {
            $sibling = $this->editor->getBlockManager()->moveDown($block);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        $this->validate($row);
        $this->validate($sibling);
        $this->persist($row);
        $this->persist($sibling);

        $createdId = $this->getViewBuilder()->buildBlock($block)->getAttributes()->getId();

        $data = [
            'removed' => [$removedId],
            'created' => $createdId,
            'rows'    => [
                $this->getViewBuilder()->buildRow($row),
                $this->getViewBuilder()->buildRow($sibling),
            ],
        ];

        return $this->buildResponse($data, self::SERIALIZE_FULL);
    }

    /**
     * Moves the block left.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function moveLeft(Request $request): Response
    {
        $block = $this->findBlockByRequest($request);

        try {
            $sibling = $this->editor->getBlockManager()->moveLeft($block);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        $row = $block->getRow();

        $this->validate($row);
        $this->persist($row);

        $data = ['blocks' => [
            $this->getViewBuilder()->buildBlock($block),
            $this->getViewBuilder()->buildBlock($sibling),
        ]];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Moves the block right.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function moveRight(Request $request): Response
    {
        $block = $this->findBlockByRequest($request);

        try {
            $sibling = $this->editor->getBlockManager()->moveRight($block);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        $row = $block->getRow();

        $this->validate($row);
        $this->persist($row);

        $data = ['blocks' => [
            $this->getViewBuilder()->buildBlock($block),
            $this->getViewBuilder()->buildBlock($sibling),
        ]];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }
}
