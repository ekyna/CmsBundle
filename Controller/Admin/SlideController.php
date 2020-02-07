<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Admin;

use Ekyna\Bundle\AdminBundle\Controller\Resource\SortableTrait;
use Ekyna\Bundle\AdminBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SlideController
 * @package Ekyna\Bundle\CmsBundle\Controller\Admin
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SlideController extends ResourceController
{
    use SortableTrait;


    /**
     * {@inheritdoc}
     */
    public function newAction(Request $request)
    {
        $this->isGranted('CREATE');

        if ($request->isXmlHttpRequest()) {
            throw $this->createNotFoundException("XHR is not supported.");
        }
        $context = $this->loadContext($request);

        /** @var \Ekyna\Bundle\CmsBundle\Entity\Slide $slide */
        $slide = $this->createNew($context);

        $resourceName = $this->config->getResourceName();
        $context->addResource($resourceName, $slide);

        $this->getOperator()->initialize($slide);

        $flow = $this->get('ekyna_cms.slide.form_flow');
        $flow->setGenericFormOptions([
            'action'            => $this->generateResourcePath($slide, 'new'),
            'method'            => 'POST',
            'attr'              => ['class' => 'form-horizontal'],
            '_redirect_enabled' => true,
        ]);
        $flow->bind($slide);

        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                $form = $flow->createForm();
            } else {
                // TODO use ResourceManager
                $event = $this->getOperator()->create($slide);

                $event->toFlashes($this->getFlashBag());

                if (!$event->hasErrors()) {
                    if ($this->hasParent() && null !== $parentResource = $this->getParentResource($context)) {
                        $redirectPath = $this->generateResourcePath($parentResource, 'show');
                    } else {
                        $redirectPath = $this->generateResourcePath($slide, 'show');
                    }

                    return $this->redirect($redirectPath);
                }
            }
        }

        $this->appendBreadcrumb(
            sprintf('%s_new', $resourceName),
            'ekyna_core.button.create'
        );

        return $this->render(
            $this->config->getTemplate('new.html'),
            $context->getTemplateVars([
                'flow' => $flow,
                'form' => $form->createView(),
            ])
        );
    }
}
