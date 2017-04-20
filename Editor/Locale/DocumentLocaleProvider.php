<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Locale;

use Ekyna\Component\Resource\Locale\LocaleProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ContentLocaleProvider
 * @package Ekyna\Bundle\CmsBundle\Editor\Locale
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class DocumentLocaleProvider implements LocaleProviderInterface, EventSubscriberInterface
{
    private array    $availableLocales;
    private string   $defaultLocale;
    private ?Request $request = null;


    /**
     * Constructor.
     *
     * @param string $defaultLocale
     * @param array  $availableLocales
     */
    public function __construct(array $availableLocales, string $defaultLocale)
    {
        $this->availableLocales = $availableLocales;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $this->request = $event->getRequest();
    }

    /**
     * @inheritDoc
     */
    public function getCurrentLocale(): string
    {
        if (null === $this->request) {
            return $this->getFallbackLocale();
        }

        return $this->request->get('_document_locale', $this->request->getLocale());
    }

    /**
     * @inheritDoc
     */
    public function getFallbackLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * @inheritDoc
     */
    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // IMPORTANT to keep priority 34.
            KernelEvents::REQUEST => [['onKernelRequest', 34]],
        ];
    }
}
