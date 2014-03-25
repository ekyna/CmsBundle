<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Resource;

use Symfony\Component\HttpFoundation\Request;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;

/**
 * ContentTrait
 */
trait ContentTrait
{
    public function contentShowAction(Request $request)
    {
        $resource = $this->findResourceOrThrowException();
        if (!$resource instanceOf ContentSubjectInterface) {
            throw new \Exception('Resource must implements ContentSubjectInterface.');
        }

        $this->isGranted('VIEW', $resource);

        // TODO: select version
        $resourceName = $this->getResourceName();

        return $this->render(
            $this->configuration->getTemplate('content_show.html'),
            array(
                $resourceName => $resource
            )
        );
    }

    public function contentEditAction(Request $request)
    {
        $resource = $this->findResourceOrThrowException();
        if (!$resource instanceOf ContentSubjectInterface) {
            throw new \Exception('Resource must implements ContentSubjectInterface.');
        }

        $this->isGranted('EDIT', $resource);

        $newContent = false;
        $resourceName = $this->getResourceName();
        // TODO: select version
        if (null === $content = $resource->getContent()) {
            $newContent = true;
            $content = $this->generateDefaultContent();
        }
        $form = $this->createForm('ekyna_content', $content);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            if($newContent) {
                $resource->addContent($content);
            }
            $this->persist($resource);

            $this->addFlash('Le contenu a été modifiée avec succès.', 'success');

            return $this->redirect(
                $this->generateUrl(
                    $this->configuration->getRoute('content'),
                    array(
                        sprintf('%sId', $resourceName) => $resource->getId()
                    )
                )
            );
        }

        return $this->render(
            $this->configuration->getTemplate('content_edit.html'),
            array(
                $resourceName => $resource,
                'form' => $form->createView()
            )
        );
    }

}
