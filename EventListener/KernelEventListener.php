<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class KernelEventListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class KernelEventListener implements EventSubscriberInterface
{
    /**
     * @var PageHelper
     */
    private $pageHelper;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var Session
     */
    private $session;


    /**
     * KernelEventListener constructor.
     * @param PageHelper $pageHelper
     * @param SecurityContextInterface $securityContext
     * @param Session $session
     */
    public function __construct(PageHelper $pageHelper, SecurityContextInterface $securityContext, Session $session)
    {
        $this->pageHelper = $pageHelper;
        $this->securityContext = $securityContext;
        $this->session = $session;
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

        if (null !== $page = $this->pageHelper->init($event->getRequest())) {
            if (!$page->getEnabled()) {
                if ($this->securityContext->isGranted('ROLE_ADMIN')) {
                    $this->session->getFlashBag()->add('warning', 'ekyna_cms.page.alert.disabled.allow_as_admin');
                    return;
                }

                $this->session->getFlashBag()->add('warning', 'ekyna_cms.page.alert.disabled.temporary_redirect');
                $event->setResponse(new RedirectResponse('/'));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 0),
        );
    }
}
