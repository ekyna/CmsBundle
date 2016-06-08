<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class BaseController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BaseController extends Controller
{
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
     * Validates the object.
     *
     * @param mixed $object
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    protected function validate($object)
    {
        return $this->get('validator')->validate($object);
    }

    /**
     * Serializes the data.
     *
     * @param array  $data
     * @param string $group
     *
     * @return mixed|string
     */
    protected function serialize($data, $group = 'Default')
    {
        $context = SerializationContext::create()->setGroups($group);

        return $this->get('serializer')->serialize($data, 'json', $context);
    }

    /**
     * Returns the editor view builder.
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\ViewBuilder
     */
    protected function getViewBuilder()
    {
        return $this->get('ekyna_cms.editor.view_builder');
    }

    /**
     * Returns the editor.
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Editor
     */
    protected function getEditor()
    {
        return $this->get('ekyna_cms.editor.editor');
    }

    /**
     * Check user authorization.
     *
     * @throws AccessDeniedHttpException
     */
    protected function checkAuthorization()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('Access denied');
        }
    }

    /**
     * Finds the row by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Entity\Row
     */
    protected function findRow($id)
    {
        if (!(is_int($id) && 0 < $id)) {
            throw new \InvalidArgumentException('Expected integer greater than zero.');
        }

        $row = $this
            ->getDoctrine()
            ->getRepository('EkynaCmsBundle:Row')
            ->find($id);

        if (null === $row) {
            throw new NotFoundHttpException('Row not found.');
        }

        return $row;
    }
}
