<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Ekyna\Bundle\CoreBundle\Modal\Modal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class BaseController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BaseController extends Controller
{
    const SERIALIZE_FULL    = 'Default';
    const SERIALIZE_LAYOUT  = 'Layout';
    const SERIALIZE_CONTENT = 'Content';

    /**
     * @var Editor
     */
    private $editor;


    /**
     * Creates a modal.
     *
     * @param string $title
     * @param mixed  $content
     * @param array  $buttons
     *
     * @return Modal
     */
    protected function createModal($title, $content = null, array $buttons = [])
    {
        $modal = new Modal($title);

        if (empty($buttons)) {
            $buttons['submit'] = [
                'id'       => 'submit',
                'label'    => 'ekyna_core.button.validate',
                'icon'     => 'glyphicon glyphicon-ok',
                'cssClass' => 'btn-success',
                'autospin' => true,
            ];
        }
        if (!array_key_exists('close', $buttons)) {
            $buttons['close'] = [
                'id'       => 'close',
                'label'    => 'ekyna_core.button.cancel',
                'icon'     => 'glyphicon glyphicon-remove',
                'cssClass' => 'btn-default',
            ];
        }

        $modal->setButtons($buttons);

        if ($content) {
            $modal->setContent($content);
        }

        return $modal;
    }

    /**
     * Handles editor exception.
     *
     * @param EditorExceptionInterface $exception
     *
     * @return Response
     */
    protected function handleException(EditorExceptionInterface $exception)
    {
        if ($this->getParameter('kernel.debug')) {
            throw $exception;
        }

        return $this->buildResponse([
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Builds the response.
     *
     * @param array  $data
     * @param string $serializationGroup
     *
     * @return Response
     */
    protected function buildResponse(array $data, $serializationGroup = 'Default')
    {
        $response = new Response($this->serialize($data, $serializationGroup));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Persists the entity and flush the entity manager.
     *
     * @param object $entity
     */
    protected function persist($entity)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($entity);
        $manager->flush();
    }

    /**
     * Validates the object.
     *
     * @param mixed $object
     *
     * @throws BadRequestHttpException
     */
    protected function validate($object)
    {
        $errorList = $this->get('validator')->validate($object);
        if (0 < $errorList->count()) {
            $message = 'Row validation failed.';
            if ($this->getParameter('kernel.debug')) {
                $messages = [];
                /** @var \Symfony\Component\Validator\ConstraintViolationInterface $error */
                foreach ($errorList as $error) {
                    $messages[] = $error->getMessage();
                }
                $message = implode(', ', $messages);
            }
            throw new BadRequestHttpException($message);
        }
    }

    /**
     * Serializes the data.
     *
     * @param array  $data
     * @param string $group
     *
     * @return mixed|string
     */
    protected function serialize($data, $group = self::SERIALIZE_FULL)
    {
        return $this->get('serializer')->serialize($data, 'json', ['groups' => [$group]]);
    }

    /**
     * Returns the editor view builder.
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\View\ViewBuilder
     */
    protected function getViewBuilder()
    {
        return $this->getEditor()->getViewBuilder();
    }

    /**
     * Returns the editor.
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Editor
     */
    protected function getEditor()
    {
        if (null !== $this->editor) {
            return $this->editor;
        }

        return $this->editor = $this
            ->get('ekyna_cms.editor.editor')
            ->setEnabled(true); // TODO Do this somewhere else
    }

    /**
     * Renders the modal.
     *
     * @param Modal $modal
     *
     * @return Response
     */
    protected function renderModal(Modal $modal)
    {
        return $this->get('ekyna_core.modal')->render($modal);
    }

    /**
     * Finds the content by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\ContentInterface
     */
    protected function findContent($id)
    {
        return $this
            ->get('ekyna_cms.content.repository')
            ->findOneById($id);
    }

    /**
     * Finds the container by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface
     */
    protected function findContainer($id)
    {
        return $this->getEditor()->getRepository()->findContainerById($id);
    }

    /**
     * Finds the row by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\RowInterface
     */
    protected function findRow($id)
    {
        return $this->getEditor()->getRepository()->findRowById($id);
    }

    /**
     * Finds the block by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface
     */
    protected function findBlock($id)
    {
        return $this->getEditor()->getRepository()->findBlockById($id);
    }

    /**
     * Finds the block by request.
     *
     * @param Request $request
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface
     */
    protected function findBlockByRequest(Request $request)
    {
        return $this->findBlock(intval($request->attributes->get('blockId')));
    }

    /**
     * Finds the row by request.
     *
     * @param Request $request
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\RowInterface
     */
    protected function findRowByRequest(Request $request)
    {
        return $this->findRow(intval($request->attributes->get('rowId')));
    }

    /**
     * Finds the container by request.
     *
     * @param Request $request
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface
     */
    protected function findContainerByRequest(Request $request)
    {
        return $this->findContainer(intval($request->attributes->get('containerId')));
    }

    /**
     * Finds the content by request.
     *
     * @param Request $request
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\ContentInterface
     */
    protected function findContentByRequest(Request $request)
    {
        return $this->findContent(intval($request->attributes->get('contentId')));
    }
}
