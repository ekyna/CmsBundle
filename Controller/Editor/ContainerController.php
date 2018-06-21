<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Ekyna\Bundle\CoreBundle\Modal\Modal;
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

        $target = is_null($container->getCopy()) ? $container : $container->getCopy();

        try {
            $row = $this->getEditor()->createDefaultRow([], $target);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $container = $this->findContainerByRequest($request);

        try {
            $response = $this->getEditor()->getContainerManager()->update($container, $request);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function layoutAction(Request $request)
    {
        $container = $this->findContainerByRequest($request);

        $data = $request->request->get('data', []);

        try {
            $this->getEditor()->getLayoutAdapter()->updateContainerLayout($container, $data);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $this->validate($container);
        $this->persist($container);

        $data = [
            'containers' => [$this->getViewBuilder()->buildContainer($container)]
        ];

        return $this->buildResponse($data, self::SERIALIZE_LAYOUT);
    }

    /**
     * Changes the container type.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeTypeAction(Request $request)
    {
        $container = $this->findContainerByRequest($request);

        $type = $request->request->get('type', null);

        $data = [];

        if ($type != $container->getType()) {
            try {
                $removed = $this->getEditor()->getContainerManager()->changeType($container, $type);
            } catch (EditorExceptionInterface $e) {
                throw new BadRequestHttpException($e->getMessage());
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request)
    {
        $container = $this->findContainerByRequest($request);
        $content = $container->getContent();

        try {
            $this->getEditor()->getContainerManager()->delete($container);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        // Stores id for front removal
        $removedId = $this->getViewBuilder()->buildContainer($container)->getAttributes()->getId();

        $this->validate($content);
        $this->persist($content);

        $data = [
            'removed'  => [$removedId],
            //'contents' => [$this->getViewBuilder()->buildContent($content)],
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
        } catch (EditorExceptionInterface $e) {
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
        } catch (EditorExceptionInterface $e) {
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
