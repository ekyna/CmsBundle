<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model\ContentInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model\RowInterface;
use Ekyna\Bundle\CmsBundle\Editor\View\ViewBuilder;
use Ekyna\Bundle\UiBundle\Model\Modal;
use Ekyna\Bundle\UiBundle\Service\Modal\ModalRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AbstractController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractController
{
    public const SERIALIZE_FULL    = 'Default';
    public const SERIALIZE_LAYOUT  = 'Layout';
    public const SERIALIZE_CONTENT = 'Content';

    protected Editor               $editor;
    private EntityManagerInterface $manager;
    private ValidatorInterface     $validator;
    private SerializerInterface    $serializer;
    private ModalRenderer          $renderer;
    private bool                   $debug;


    /**
     * Constructor.
     *
     * @param Editor                 $editor
     * @param EntityManagerInterface $manager
     * @param ValidatorInterface     $validator
     * @param SerializerInterface    $serializer
     * @param ModalRenderer          $renderer
     * @param bool                   $debug
     */
    public function __construct(
        Editor $editor,
        EntityManagerInterface $manager,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        ModalRenderer $renderer,
        bool $debug
    ) {
        $this->editor = $editor;
        $this->manager = $manager;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->renderer = $renderer;
        $this->debug = $debug;
    }

    /**
     * Creates a modal.
     *
     * @param string $title
     * @param mixed  $content
     * @param array  $buttons
     *
     * @return Modal
     */
    protected function createModal(string $title, $content = null, array $buttons = []): Modal
    {
        $modal = new Modal($title);

        if (empty($buttons)) {
            $buttons['submit'] = array_replace(Modal::BTN_SUBMIT, [
                'label' => 'button.validate',
            ]);
        }
        if (!array_key_exists('close', $buttons)) {
            $buttons['close'] = Modal::BTN_CLOSE;
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
    protected function handleException(EditorExceptionInterface $exception): Response
    {
        if ($this->debug) {
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
    protected function buildResponse(array $data, string $serializationGroup = 'Default'): Response
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
    protected function persist(object $entity): void
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }

    /**
     * Validates the object.
     *
     * @param mixed $object
     *
     * @throws BadRequestHttpException
     */
    protected function validate($object): void
    {
        $errorList = $this->validator->validate($object);

        if (0 === $errorList->count()) {
            return;
        }

        $message = 'Row validation failed.';
        if ($this->debug) {
            $messages = [];
            /** @var ConstraintViolationInterface $error */
            foreach ($errorList as $error) {
                $messages[] = $error->getMessage();
            }
            $message = implode(', ', $messages);
        }

        throw new BadRequestHttpException($message);
    }

    /**
     * Serializes the data.
     *
     * @param array  $data
     * @param string $group
     *
     * @return string
     */
    protected function serialize(array $data, string $group = self::SERIALIZE_FULL): string
    {
        return $this->serializer->serialize($data, 'json', ['groups' => [$group]]);
    }

    /**
     * Returns the editor view builder.
     *
     * @return ViewBuilder
     */
    protected function getViewBuilder(): ViewBuilder
    {
        return $this->editor->getViewBuilder();
    }

    /**
     * Renders the modal.
     *
     * @param Modal $modal
     *
     * @return Response
     */
    protected function renderModal(Modal $modal): Response
    {
        return $this->renderer->render($modal);
    }

    /**
     * Finds the content by id.
     *
     * @param int $id
     *
     * @return ContentInterface
     */
    protected function findContent(int $id): ContentInterface
    {
        return $this->editor->getRepository()->findContentById($id);
    }

    /**
     * Finds the container by id.
     *
     * @param int $id
     *
     * @return ContainerInterface
     */
    protected function findContainer(int $id): ContainerInterface
    {
        return $this->editor->getRepository()->findContainerById($id);
    }

    /**
     * Finds the row by id.
     *
     * @param int $id
     *
     * @return RowInterface
     */
    protected function findRow(int $id): RowInterface
    {
        return $this->editor->getRepository()->findRowById($id);
    }

    /**
     * Finds the block by id.
     *
     * @param int $id
     *
     * @return BlockInterface
     */
    protected function findBlock(int $id): BlockInterface
    {
        return $this->editor->getRepository()->findBlockById($id);
    }

    /**
     * Finds the block by request.
     *
     * @param Request $request
     *
     * @return BlockInterface
     */
    protected function findBlockByRequest(Request $request): BlockInterface
    {
        return $this->findBlock($request->attributes->getInt('blockId'));
    }

    /**
     * Finds the row by request.
     *
     * @param Request $request
     *
     * @return RowInterface
     */
    protected function findRowByRequest(Request $request): RowInterface
    {
        return $this->findRow($request->attributes->getInt('rowId'));
    }

    /**
     * Finds the container by request.
     *
     * @param Request $request
     *
     * @return ContainerInterface
     */
    protected function findContainerByRequest(Request $request): ContainerInterface
    {
        return $this->findContainer($request->attributes->getInt('containerId'));
    }

    /**
     * Finds the content by request.
     *
     * @param Request $request
     *
     * @return ContentInterface
     */
    protected function findContentByRequest(Request $request): ContentInterface
    {
        return $this->findContent($request->attributes->getInt('contentId'));
    }
}
