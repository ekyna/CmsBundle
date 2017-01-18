<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Ekyna\Bundle\CoreBundle\Modal\Modal;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class BlockController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BlockController extends BaseController
{
    /**
     * Edits the block.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);

        try {
            $response = $this->getEditor()->getBlockManager()->update($block, $request);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($response instanceof Modal) {
            return $this->renderModal($response);
        } elseif ($response instanceof Response) {
            return $response;
        }

        $this->validate($block);
        $this->persist($block);

        $view = $this->getViewBuilder()->buildBlock($block);

        if (0 < strlen($block->getName())) {
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function layoutAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);

        $data = $request->request->get('data', []);

        try {
            $this->getEditor()->getLayoutAdapter()->updateBlockLayout($block, $data);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeTypeAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);
        $type = $request->request->get('type', null);
        $removed = [];

        if ($type != $block->getType()) {
            foreach ($this->getViewBuilder()->buildBlock($block)->widgets as $widget) {
                $removed[] = $widget->getAttributes()->getId();
            }

            try {
                $this->getEditor()->getBlockManager()->changeType($block, $type);
            } catch (EditorExceptionInterface $e) {
                throw new BadRequestHttpException($e->getMessage());
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);

        try {
            $this->getEditor()->getBlockManager()->delete($block);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        // Stores id for front removal
        $removedId = $this->getViewBuilder()->buildBlock($block)->getAttributes()->getId();
        $row = $block->getRow();

        $this->validate($row);
        $this->persist($row);

        $data = [
            'removed' => [$removedId],
            'rows'    => [$this->getViewBuilder()->buildRow($row)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Moves the block up.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveUpAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);
        $row = $block->getRow();

        $removedId = $this->getViewBuilder()->buildBlock($block)->getAttributes()->getId();

        try {
            $sibling = $this->getEditor()->getBlockManager()->moveUp($block);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveDownAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);
        $row = $block->getRow();

        $removedId = $this->getViewBuilder()->buildBlock($block)->getAttributes()->getId();

        try {
            $sibling = $this->getEditor()->getBlockManager()->moveDown($block);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveLeftAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);

        try {
            $sibling = $this->getEditor()->getBlockManager()->moveLeft($block);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveRightAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);

        try {
            $sibling = $this->getEditor()->getBlockManager()->moveRight($block);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
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
