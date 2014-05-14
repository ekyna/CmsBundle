<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Resource;

use Doctrine\Common\Collections\ArrayCollection;
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
            // TODO: Test if generateDefaultContent method exists ? User abstract method definition ?
            $content = $this->generateDefaultContent();
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

    protected function generateDefaultContent()
    {
        $layout = $this->container->get('ekyna_cms.layout_registry')->get('default');
    
        $blocks = new ArrayCollection();
        foreach ($layout->getConfiguration() as $config) {
            $key = sprintf('ekyna_cms.%s_block.class', $config['type']);
            if(!$this->container->hasParameter($key)) {
                throw new \InvalidArgumentException(sprintf('Unknown block type "%s".', $config['type']));
            }
            $class = $this->container->getParameter($key);
            $block = new $class;
            $block
                ->setWidth($config['width'])
                ->setRow($config['row'])
                ->setColumn($config['column'])
            ;
            $blocks[] = $block;
        }
    
        $content = new Content();
        $content
            ->setBlocks($blocks)
            ->setVersion(1)
        ;
    
        return $content;
    }
}
