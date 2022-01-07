<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Updater;

use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\SettingBundle\Event\BuildRedirectionEvent;
use Ekyna\Bundle\SettingBundle\Event\DiscardRedirectionEvent;
use Ekyna\Bundle\SettingBundle\Event\RedirectionEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use function array_key_exists;

/**
 * Class PageRedirectionUpdater
 * @package Ekyna\Bundle\CmsBundle\Service\Updater
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageRedirectionUpdater
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Discards redirections for the page.
     */
    public function discardPageRedirections(PageInterface $page): void
    {
        if (!$page->isEnabled()) {
            return;
        }

        foreach ($page->getTranslations() as $translation) {
            $event = new DiscardRedirectionEvent($translation->getPath());
            $this->dispatcher->dispatch($event, RedirectionEvents::DISCARD);
        }
    }

    /**
     * Builds redirections for the page.
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
            if (!$parentPage->isEnabled()) {
                continue;
            }

            // Store "to" paths for each locale
            foreach ($parentPage->getTranslations() as $locale => $translation) {
                if (!array_key_exists($locale, $redirections)) {
                    continue;
                }

                $redirections[$locale]['to'] = $translation->getPath();
                unset($locales[$locale]);

                // Check that all locales has been handled
                if (empty($locales)) {
                    break 2;
                }
            }
        }

        if (empty($redirections)) {
            return;
        }

        foreach ($redirections as $redirection) {
            if (!array_key_exists('from', $redirection) || !array_key_exists('to', $redirection)) {
                continue;
            }

            $event = new BuildRedirectionEvent($redirection['from'], $redirection['to'], true);

            $this->dispatcher->dispatch($event, RedirectionEvents::BUILD);
        }
    }
}
