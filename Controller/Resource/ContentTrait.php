<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Resource;

use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Ekyna\Bundle\CmsBundle\Entity\Content;
use Symfony\Component\HttpFoundation\Request;

/**
 * ContentTrait
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait ContentTrait
{
    public function contentShowAction(Request $request)
    {
        $context = $this->loadContext($request);
        $resourceName = $this->config->getResourceName();
        $resource = $context->getResource($resourceName);

        $this->isGranted('VIEW', $resource);

        if (!$resource instanceOf ContentSubjectInterface) {
            throw new \Exception('Resource must implements ContentSubjectInterface.');
        }

        // TODO: select version

        // TODO: Breadcrumb

        return $this->render(
            $this->config->getTemplate('content_show.html'),
            $context->getTemplateVars()
        );
    }

    public function contentEditAction(Request $request)
    {
        $context = $this->loadContext($request);
        $resourceName = $this->config->getResourceName();
        $resource = $context->getResource($resourceName);

        $this->isGranted('EDIT', $resource);

        if (!$resource instanceOf ContentSubjectInterface) {
            throw new \Exception('Resource must implements ContentSubjectInterface.');
        }

        $newContent = false;

        // TODO: select version
        if (null === $content = $resource->getContent()) {
            $newContent = true;
            $content = new Content();
        }
        $form = $this->createForm('ekyna_cms_content', $content, array(
            'admin_mode' => true,
            '_redirect_enabled' => true,
            '_footer' => array(
                'cancel_path' => $this->generateUrl($this->config->getRoute('list')),
            ),
        ));

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            if($newContent) {
                $resource->addContent($content);
            }
            $this->persist($resource);

            $this->addFlash('Le contenu a été modifiée avec succès.', 'success');

            if (null !== $redirectPath = $form->get('_redirect')->getData()) {
                return $this->redirect($redirectPath);
            }

            return $this->redirect(
                $this->generateUrl(
                    $this->config->getRoute('content'),
                    $context->getIdentifiers(true)
                )
            );
        }

        // TODO: Breadcrumb

        return $this->render(
            $this->config->getTemplate('content_edit.html'),
            $context->getTemplateVars(array(
                'form' => $form->createView()
            ))
        );
    }
}
