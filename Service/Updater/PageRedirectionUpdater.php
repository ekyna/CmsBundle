<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Updater;

use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\SettingBundle\Event\BuildRedirectionEvent;
use Ekyna\Bundle\SettingBundle\Event\DiscardRedirectionEvent;
use Ekyna\Bundle\SettingBundle\Event\RedirectionEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PageRedirectionUpdater
 * @package Ekyna\Bundle\CmsBundle\Service\Updater
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageRedirectionUpdater
{
    private EventDispatcherInterface $dispatcher;
    private array                    $locales;


    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param array                    $locales
     */
    public function __construct(EventDispatcherInterface $dispatcher, array $locales)
    {
        $this->dispatcher = $dispatcher;
        $this->locales = $locales;
    }

    /**
     * Discards redirections for the page.
     *
     * @param PageInterface $page
     */
    public function discardPageRedirections(PageInterface $page): void
    {
        if (!$page->isEnabled()) {
            return;
        }

        foreach ($page->getTranslations() as $locale => $translation) {
            // TODO use url generator or i18n routing prefix strategy
            $localePrefix = $locale != 'fr' ? '/' . $locale : '';
            $event = new DiscardRedirectionEvent($localePrefix . $translation->getPath());
            $this->dispatcher->dispatch($event, RedirectionEvents::DISCARD);
        }
    }

    /**
     * Builds redirections for the page.
     *
     * @param PageInterface $page
     */
    public function buildPageRedirections(PageInterface $page): void
    {
        if ($page->isEnabled()) {
            return;
        }

        $redirections = [];

        // Store "from" paths for each locale
        $locales = [];
        foreach ($page->getTranslations() as $locale => $translation) {
            $locales[$locale] = $locale;
            $redirections[$locale] = [
                'from' => $translation->getPath(),
            ];
        }

        // Find the first enabled ancestor
        $parentPage = $page;
        while (null !== $parentPage = $parentPage->getParent()) {
            if ($parentPage->isEnabled()) {
                // Store "to" paths for each locale
                foreach ($parentPage->getTranslations() as $locale => $translation) {
                    if (array_key_exists($locale, $redirections)) {
                        $redirections[$locale]['to'] = $translation->getPath();
                        unset($locales[$locale]);
                    }

                    // Check that all locales has been handled
                    if (empty($locales)) {
                        break 2;
                    }
                }
            }
        }

        if (!empty($redirections)) {
            foreach ($redirections as $locale => $redirection) {
                if (!(array_key_exists('from', $redirection) && array_key_exists('to', $redirection))) {
                    continue;
                }
                // TODO use url generator or i18n routing prefix strategy
                $localePrefix = $locale != 'fr' ? '/' . $locale : '';

                $event = new BuildRedirectionEvent(
                    $localePrefix . $redirection['from'],
                    $localePrefix . $redirection['to'],
                    true
                );

                $this->dispatcher->dispatch($event, RedirectionEvents::BUILD);
            }
        }
    }
}
