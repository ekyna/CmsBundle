<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ContainerController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContainerController extends BaseController
{
    /**
     * Create and append a new row to the container.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createRowAction(Request $request)
    {
        $container = $this->findContainer(intval($request->attributes->get('containerId')));

        try {
            $row = $this->getEditor()->createDefaultRow([], $container);
        } catch (EditorException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $this->validate($container);
        $this->persist($container);

        $viewBuilder = $this->getViewBuilder();

        $data = [
            'created'    => $viewBuilder->buildRow($row)->attributes['id'],
            'containers' => [$viewBuilder->buildContainer($container)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_FULL);
    }

    /**
     * Edit the container.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $container = $this->findContainerByRequest($request);

        try {
            $response = $this->getEditor()->getContainerManager()->update($container, $request);
        } catch (EditorException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($response instanceof Response) {
            return $response;
        }

        $this->validate($container);
        $this->persist($container);

        $data = [
            'containers' => [$this->getViewBuilder()->buildContainer($container)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_CONTENT);
    }

    /**
     * Remove the container.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request)
    {
        $container = $this->findContainerByRequest($request);

        try {
            $this->getEditor()->getContainerManager()->delete($container);
        } catch (EditorException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        // Stores id for front removal
        $removedId = $this->getViewBuilder()->buildContainer($container)->attributes['id'];
        $content = $container->getContent();

        $this->validate($content);
        $this->persist($content);

        $data = [
            'removed'  => [$removedId],
            'contents' => [$this->getViewBuilder()->buildContent($content)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Move up the container.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws BadRequestHttpException
     */
    public function moveUpAction(Request $request)
    {
        $container = $this->findContainerByRequest($request);

        try {
            $sibling = $this->getEditor()->getContainerManager()->moveUp($container);
        } catch (EditorException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $content = $container->getContent();

        $this->validate($content);
        $this->persist($content);

        $data = ['containers' => [
            $this->getViewBuilder()->buildContainer($container),
            $this->getViewBuilder()->buildContainer($sibling),
        ]];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Move down the container.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws BadRequestHttpException
     */
    public function moveDownAction(Request $request)
    {
        $container = $this->findContainerByRequest($request);

        try {
            $sibling = $this->getEditor()->getContainerManager()->moveDown($container);
        } catch (EditorException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $content = $container->getContent();

        $this->validate($content);
        $this->persist($content);

        $data = ['containers' => [
            $this->getViewBuilder()->buildContainer($container),
            $this->getViewBuilder()->buildContainer($sibling),
        ]];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }
}
