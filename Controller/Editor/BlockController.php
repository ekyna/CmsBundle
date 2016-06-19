<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorException;
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
     * Edit the block.
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
        } catch (EditorException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($response instanceof Response) {
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
     * Change the block type.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeTypeAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);
        $type = $request->request->get('type', null);

        try {
            $this->getEditor()->getBlockManager()->changeType($block, $type);
        } catch (EditorException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $this->validate($block);
        $this->persist($block);

        $data = ['blocks' => [
            $this->getViewBuilder()->buildBlock($block)
        ]];

        return $this->buildResponse($data, self::SERIALIZE_CONTENT);
    }

    /**
     * Remove the block.
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
        } catch (EditorException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        // Stores id for front removal
        $removedId = $this->getViewBuilder()->buildBlock($block)->attributes['id'];
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
     * Expand the block.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function expandAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);

        try {
            $sibling = $this->getEditor()->getBlockManager()->expand($block);
        } catch (EditorException $e) {
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
     * Compress the block.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function compressAction(Request $request)
    {
        $block = $this->findBlockByRequest($request);

        try {
            $sibling = $this->getEditor()->getBlockManager()->compress($block);
        } catch (EditorException $e) {
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
     * Move left the block.
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
        } catch (EditorException $e) {
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
     * Move right the block.
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
        } catch (EditorException $e) {
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
     * Move up the block.
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
     * Move down the block.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveDownAction(Request $request)
    {
        throw new \Exception('Not yet implemented'); // TODO
    }
}
