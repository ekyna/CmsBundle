<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class RowController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class RowController extends BaseController
{
    /**
     * Create and append a new block to the row.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws BadRequestHttpException
     */
    public function createBlockAction(Request $request)
    {
        $row = $this->findRowByRequest($request);
        $type = $request->request->get('type', null);

        try {
            $block = $this->getEditor()->createDefaultBlock($type, [], $row);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $this->validate($row);
        $this->persist($row);

        $viewBuilder = $this->getViewBuilder();

        $data = [
            'created' => $viewBuilder->buildBlock($block)->getAttributes()->getId(),
            'rows'    => [$viewBuilder->buildRow($row)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_FULL);
    }

    /**
     * Edit the row.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        throw new \Exception('Not yet implemented'); // TODO
    }

    /**
     * Updates the row layout.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function layoutAction(Request $request)
    {
        $row = $this->findRowByRequest($request);

        $data = $request->request->get('data', []);

        try {
            $this->getEditor()->getLayoutAdapter()->updateRowLayout($row, $data);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $this->validate($row);
        $this->persist($row);

        $data = [
            'rows' => [$this->getViewBuilder()->buildRow($row)]
        ];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Remove the row.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws BadRequestHttpException
     */
    public function removeAction(Request $request)
    {
        $row = $this->findRowByRequest($request);
        $container = $row->getContainer();

        try {
            $this->getEditor()->getRowManager()->delete($row);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        // Stores id for front removal
        $removedId = $this->getViewBuilder()->buildRow($row)->getAttributes()->getId();

        $this->validate($container);
        $this->persist($container);

        $data = [
            'removed'    => [$removedId],
            'containers' => [$this->getViewBuilder()->buildContainer($container)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Move up the row.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws BadRequestHttpException
     */
    public function moveUpAction(Request $request)
    {
        $row = $this->findRowByRequest($request);

        try {
            $sibling = $this->getEditor()->getRowManager()->moveUp($row);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $container = $row->getContainer();

        $this->validate($container);
        $this->persist($container);

        $data = ['rows' => [
            $this->getViewBuilder()->buildRow($row),
            $this->getViewBuilder()->buildRow($sibling),
        ]];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Move down the row.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws BadRequestHttpException
     */
    public function moveDownAction(Request $request)
    {
        $row = $this->findRowByRequest($request);

        try {
            $sibling = $this->getEditor()->getRowManager()->moveDown($row);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $container = $row->getContainer();

        $this->validate($container);
        $this->persist($container);

        $data = ['rows' => [
            $this->getViewBuilder()->buildRow($row),
            $this->getViewBuilder()->buildRow($sibling),
        ]];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }
}
