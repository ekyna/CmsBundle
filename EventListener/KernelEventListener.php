<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Service\Helper\PageHelper;
use League\Uri\Uri;
use League\Uri\UriModifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatableMessage;

use function preg_match;
use function strpos;

/**
 * Class KernelEventListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class KernelEventListener implements EventSubscriberInterface
{
    private Editor                        $editor;
    private PageHelper                    $pageHelper;
    private AuthorizationCheckerInterface $authorizationChecker;
    private RequestStack                  $requestStack;
    private string                        $filterRegExp;
    private bool $enabled = false;


    /**
     * KernelEventListener constructor.
     *
     * @param Editor                        $editor
     * @param PageHelper                    $pageHelper
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param RequestStack                  $requestStack
     * @param string                        $filterRegExp
     */
    public function __construct(
        Editor $editor,
        PageHelper $pageHelper,
        AuthorizationCheckerInterface $authorizationChecker,
        RequestStack $requestStack,
        string $filterRegExp = '~^/(admin|api|css|js|media|images|_(profiler|wdt))~'
    ) {
        $this->editor = $editor;
        $this->pageHelper = $pageHelper;
        $this->authorizationChecker = $authorizationChecker;
        $this->requestStack = $requestStack;
        $this->filterRegExp = $filterRegExp;
    }

    /**
     * Kernel request event handler.
     *
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->getRequestType() !== HttpKernelInterface::MAIN_REQUEST) {
            return;
        }

        $request = $event->getRequest();

        // Enable editor for admin routes
        $route = $request->attributes->get('_route');
        if (0 === strpos($route, 'admin_ekyna_cms_editor_')) {
            $this->editor->setEnabled(true);
            $this->enabled = true;

            return;
        }

        // Skip non public pages
        if (preg_match($this->filterRegExp, $request->getPathInfo())) {
            return;
        }

        if (1 === $request->query->getInt(Editor::URL_PARAMETER)) {
            $this->editor->setEnabled(true);
            $this->enabled = true;

            if (0 < $width = $request->request->getInt('cms_viewport_width')) {
                $this->editor->setViewportWidth($width);
            }
        }

        if (null === $page = $this->pageHelper->init($request)) {
            return;
        }

        if ($page->isEnabled()) {
            return;
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $message = new TranslatableMessage('page.alert.disabled.allow_as_admin', [], 'EkynaCms');
            $this->requestStack->getSession()->getFlashBag()->add('warning', $message);

            return;
        }

        $message = new TranslatableMessage('page.alert.disabled.temporary_redirect', [], 'EkynaCms');
        $this->requestStack->getSession()->getFlashBag()->add('warning', $message);

        throw new NotFoundHttpException('Disabled page.');
    }

    /**
     * Kernel response event.
     *
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$this->enabled) {
            return;
        }

        $response = $event->getResponse();

        if ($response->getStatusCode() === Response::HTTP_FOUND && $response->headers->has('Location')) {
            $uri = Uri::createFromString($response->headers->get('Location'));
            $uri = UriModifier::appendQuery($uri, Editor::URL_PARAMETER . '=1');
            $response->headers->set('Location', (string)$uri);
        }

        $response
            ->setSharedMaxAge(0)
            ->setMaxAge(0)
            ->setExpires()
            ->setLastModified()
            ->setPrivate();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST  => ['onKernelRequest', 0],
            KernelEvents::RESPONSE => ['onKernelResponse', 0],
        ];
    }
}
