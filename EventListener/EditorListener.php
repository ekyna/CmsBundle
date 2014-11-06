<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * EditorListener.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EditorListener implements EventSubscriberInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;
    
    /**
     * @param \Twig_Environment                                         $twig
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     */
    public function __construct(\Twig_Environment $twig, SecurityContextInterface $securityContext)
    {
        $this->twig = $twig;
        $this->securityContext = $securityContext;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if (!$event->isMasterRequest()
            || $request->isXmlHttpRequest()
            || $response->isRedirection()
            || !$request->headers->get('X-CmsEditor-Injection', false)
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $request->getRequestFormat()
            || !$this->securityContext->isGranted('ROLE_ADMIN')
        ) {
            return;
        }

        $this->injectEditor($response);
    }

    /**
     * Injects the cms editor into the given Response.
     *
     * @param Response $response
     */
    protected function injectEditor(Response $response)
    {
        if (function_exists('mb_stripos')) {
            $posrFunction   = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction   = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();
        $pos = $posrFunction($content, '</body>');

        if (false !== $pos) {
            $editor = $this->twig->render('EkynaCmsBundle:Editor:editor.html.twig');
            $content = $substrFunction($content, 0, $pos)."\n".$editor."\n".$substrFunction($content, $pos);
            $response
                ->setContent($content)
                ->setMaxAge(0)
                ->setSharedMaxAge(0)
                ->setPrivate()
            ;
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -128),
        );
    }
}
