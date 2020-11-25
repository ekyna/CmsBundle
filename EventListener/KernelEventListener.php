<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use League\Uri\Modifiers\AppendQuery;
use League\Uri\Uri;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class KernelEventListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class KernelEventListener implements EventSubscriberInterface
{
    /**
     * @var Editor
     */
    private $editor;

    /**
     * @var PageHelper
     */
    private $pageHelper;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var Session
     */
    private $session;


    /**
     * KernelEventListener constructor.
     *
     * @param Editor                        $editor
     * @param PageHelper                    $pageHelper
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param Session                       $session
     */
    public function __construct(
        Editor $editor,
        PageHelper $pageHelper,
        AuthorizationCheckerInterface $authorizationChecker,
        Session $session
    ) {
        $this->editor = $editor;
        $this->pageHelper = $pageHelper;
        $this->authorizationChecker = $authorizationChecker;
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST  => ['onKernelRequest', 0],
            KernelEvents::RESPONSE => ['onKernelResponse', 0],
        ];
    }

    /**
     * Kernel request event handler.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $request = $event->getRequest();

        if (1 === intval($request->query->get(Editor::URL_PARAMETER, 0))) {
            $this->editor->setEnabled(true);
        }
        if (0 < intval($width = $request->request->get('cms_viewport_width', 0))) {
            $this->editor->setViewportWidth($width);
        }

        if (null !== $page = $this->pageHelper->init($request)) {
            if (!$page->isEnabled()) {
                if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                    $this->session->getFlashBag()->add('warning', 'ekyna_cms.page.alert.disabled.allow_as_admin');

                    return;
                }

                $this->session->getFlashBag()->add('warning', 'ekyna_cms.page.alert.disabled.temporary_redirect');
                throw new NotFoundHttpException('Disabled page.');
            }
        }
    }

    /**
     * Kernel response event.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->editor->isEnabled()) {
            return;
        }

        $response = $event->getResponse();

        if ($response->getStatusCode() === Response::HTTP_FOUND && $response->headers->has('Location')) {
            $uri = Uri::createFromString($response->headers->get('Location'));
            $uri = (new AppendQuery(Editor::URL_PARAMETER . '=1'))->process($uri);
            $response->headers->set('Location', (string)$uri);
        }

        $response
            ->setSharedMaxAge(0)
            ->setMaxAge(0)
            ->setExpires(null)
            ->setLastModified(null)
            ->setPrivate();
    }
}
