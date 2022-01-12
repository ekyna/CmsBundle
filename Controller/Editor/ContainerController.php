<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Ekyna\Bundle\UiBundle\Model\Modal;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContainerController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContainerController extends AbstractController
{
    /**
     * Create and append a new row to the container.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createRow(Request $request): Response
    {
        $container = $this->findContainer($request->attributes->getInt('containerId'));

        $target = is_null($container->getCopy()) ? $container : $container->getCopy();

        try {
            $row = $this->editor->createDefaultRow([], $target);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        $this->validate($target);
        $this->persist($target);

        $viewBuilder = $this->getViewBuilder();

        $data = [
            'created'    => $viewBuilder->buildRow($row)->getAttributes()->getId(),
            'containers' => [$viewBuilder->buildContainer($container)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_FULL);
    }

    /**
     * Edit the container.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Request $request): Response
    {
        $container = $this->findContainerByRequest($request);

        try {
            $response = $this->editor->getContainerManager()->update($container, $request);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        if ($response instanceof Modal) {
            return $this->renderModal($response);
        } elseif ($response instanceof Response) {
            return $response;
        }

        $this->validate($container);
        $this->persist($container);

        $data = [
            'containers' => [$this->getViewBuilder()->buildContainer($container)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_FULL);
    }

    /**
     * Updates the container layout.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function layout(Request $request): Response
    {
        $container = $this->findContainerByRequest($request);

        $data = (array)$request->request->get('data', []);

        try {
            $this->editor->getLayoutAdapter()->updateContainerLayout($container, $data);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        $this->validate($container);
        $this->persist($container);

        $data = [
            'containers' => [$this->getViewBuilder()->buildContainer($container)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Changes the container type.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function changeType(Request $request): Response
    {
        $container = $this->findContainerByRequest($request);

        $type = $request->request->get('type');

        $data = [];

        if ($type != $container->getType()) {
            try {
                $removed = $this->editor->getContainerManager()->changeType($container, $type);
            } catch (EditorExceptionInterface $e) {
                return $this->handleException($e);
            }

            if (!empty($removed)) {
                $data['removed'] = $removed;
            }

            $this->validate($container);
            $this->persist($container);
        }

        $data['containers'] = [$this->getViewBuilder()->buildContainer($container)];

        return $this->buildResponse($data, self::SERIALIZE_FULL);
    }

    /**
     * Remove the container.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function remove(Request $request): Response
    {
        $container = $this->findContainerByRequest($request);
        $content = $container->getContent();

        try {
            $this->editor->getContainerManager()->delete($container);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        // Stores id for front removal
        $removedId = $this->getViewBuilder()->buildContainer($container)->getAttributes()->getId();

        $this->validate($content);
        $this->persist($content);

        $data = [
            'removed' => [$removedId],
            //'contents' => [$this->getViewBuilder()->buildContent($content)],
        ];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Move up the container.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function moveUp(Request $request): Response
    {
        $container = $this->findContainerByRequest($request);

        try {
            $sibling = $this->editor->getContainerManager()->moveUp($container);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        $content = $container->getContent();

        $this->validate($content);
        $this->persist($content);

        $data = [
            'containers' => [
                $this->getViewBuilder()->buildContainer($container),
                $this->getViewBuilder()->buildContainer($sibling),
            ],
        ];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Move down the container.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function moveDown(Request $request): Response
    {
        $container = $this->findContainerByRequest($request);

        try {
            $sibling = $this->editor->getContainerManager()->moveDown($container);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        $content = $container->getContent();

        $this->validate($content);
        $this->persist($content);

        $data = [
            'containers' => [
                $this->getViewBuilder()->buildContainer($container),
                $this->getViewBuilder()->buildContainer($sibling),
            ],
        ];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }
}
