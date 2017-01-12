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

        $data = [
            'blocks' => [$this->getViewBuilder()->buildBlock($block)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_CONTENT);
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

        $removed = [];
        foreach ($this->getViewBuilder()->buildBlock($block)->widgets as $widget) {
            $removed[] = $widget->getAttributes()->getId();
        }

        $type = $request->request->get('type', null);

        try {
            $this->getEditor()->getBlockManager()->changeType($block, $type);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $this->validate($block);
        $this->persist($block);

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
        throw new \Exception('Not yet implemented'); // TODO
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
        throw new \Exception('Not yet implemented'); // TODO
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

    /**
     * Pulls the block.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pullAction(Request $request)
    {
        throw new \Exception('Not yet implemented'); // TODO
    }

    /**
     * Pushes the block.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pushAction(Request $request)
    {
        throw new \Exception('Not yet implemented'); // TODO
    }

    /**
     * Offsets the block to the left.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function offsetLeftAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);

        $this->getEditor()->getLayoutAdapter()->offsetLeftBlock($block);

        $this->validate($block);
        $this->persist($block);

        $data = ['blocks' => [
            $this->getViewBuilder()->buildBlock($block),
        ]];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Offsets the block to the right.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function offsetRightAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);

        $this->getEditor()->getLayoutAdapter()->offsetRightBlock($block);

        $this->validate($block);
        $this->persist($block);

        $data = ['blocks' => [
            $this->getViewBuilder()->buildBlock($block),
        ]];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Compresses the block.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function compressAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);

        $this->getEditor()->getLayoutAdapter()->compressBlock($block);

        $this->validate($block);
        $this->persist($block);

        $data = ['blocks' => [
            $this->getViewBuilder()->buildBlock($block),
        ]];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Expands the block.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function expandAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);

        $this->getEditor()->getLayoutAdapter()->expandBlock($block);

        $this->validate($block);
        $this->persist($block);

        $data = ['blocks' => [
            $this->getViewBuilder()->buildBlock($block),
        ]];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }
}
