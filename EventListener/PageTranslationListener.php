<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Ekyna\Bundle\CmsBundle\Event\PageEvents;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Model\PageTranslationInterface;
use Ekyna\Bundle\CmsBundle\Service\Helper\CacheHelper;
use Ekyna\Bundle\SettingBundle\Event\BuildRedirectionEvent;
use Ekyna\Bundle\SettingBundle\Event\RedirectionEvents;
use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Ekyna\Component\Resource\Exception\UnexpectedTypeException;
use Ekyna\Component\Resource\Persistence\PersistenceHelperInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use function rtrim;
use function str_replace;
use function strlen;
use function strpos;
use function substr;

/**
 * Class PageTranslationListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageTranslationListener implements EventSubscriberInterface
{
    private PersistenceHelperInterface $persistenceHelper;
    private EventDispatcherInterface   $eventDispatcher;
    private CacheHelper                $cacheHelper;

    private array $redirections = [];

    public function __construct(
        PersistenceHelperInterface $persistenceHelper,
        EventDispatcherInterface   $eventDispatcher,
        CacheHelper                $cacheHelper
    ) {
        $this->persistenceHelper = $persistenceHelper;
        $this->eventDispatcher = $eventDispatcher;
        $this->cacheHelper = $cacheHelper;
    }

    public function onUpdate(ResourceEventInterface $event): void
    {
        $translation = $this->getTranslationFromEvent($event);

        if (!$this->persistenceHelper->isChanged($translation, 'path')) {
            return;
        }

        [$from, $to] = $this->persistenceHelper->getChangeSet($translation, 'path');
        if ($from && $to) {
            $this->redirections[] = [
                'from' => $from,
                'to'   => $to,
            ];
        }

        $page = $translation->getTranslatable();

        $this->updateChildren($translation->getTranslatable(), $from, $to, $translation->getLocale());

        $this->cacheHelper->purgePageCache($page);
        $this->cacheHelper->purgeRoutesCache();
    }

    private function updateChildren(PageInterface $page, string $from, string $to, string $locale): void
    {
        if (!$page->hasChildren()) {
            return;
        }

        foreach ($page->getChildren() as $child) {
            if (!$child->hasTranslationForLocale($locale)) {
                continue;
            }

            $translation = $child->translate($locale);
            $oldPath = $translation->getPath();

            if (0 !== strpos($oldPath, $from)) {
                continue;
            }

            $newPath = str_replace($from, $to, $oldPath);
            // Full slug will be built by TreeTranslationSlugHandler
            $translation->setPath(substr($newPath, strlen(rtrim($to, '/'))));
            $this->persistenceHelper->persistAndRecompute($translation, true);

            if (!empty($oldPath) && !empty($newPath) && ($oldPath !== $newPath)) {
                $this->redirections[] = [
                    'from' => $oldPath,
                    'to'   => $newPath,
                ];
            }
        }
    }

    public function onDelete(ResourceEventInterface $event): void
    {
        $translation = $this->getTranslationFromEvent($event);

        if (null === $page = $this->getTranslationPage($translation)) {
            return;
        }

        $this->cacheHelper->purgePageCache($page);
        $this->cacheHelper->purgeRoutesCache();

        if (empty($from = $this->getTranslationPath($translation))) {
            return;
        }

        $locale = $translation->getLocale();

        if (null === $parent = $this->getLocalizedParentTranslation($page, $locale)) {
            return;
        }

        if (empty($to = $parent->getPath()) || ($to === $from)) {
            return;
        }

        $this->redirections[] = [
            'from' => $from,
            'to'   => $to,
        ];
    }

    private function getTranslationPage(PageTranslationInterface $translation): ?PageInterface
    {
        if ($page = $translation->getTranslatable()) {
            return $page;
        }

        if (empty($cs = $this->persistenceHelper->getChangeSet($translation, 'translatable'))) {
            return null;
        }

        return $cs[0] ?? null;
    }

    private function getTranslationPath(PageTranslationInterface $translation): ?string
    {
        if ($path = $translation->getPath()) {
            return $path;
        }

        if (empty($cs = $this->persistenceHelper->getChangeSet($translation, 'path'))) {
            return null;
        }

        return $cs[0] ?? null;
    }

    private function getLocalizedParentTranslation(PageInterface $page, string $locale): ?PageTranslationInterface
    {
        if (null === $parent = $page->getParent()) {
            if (empty($cs = $this->persistenceHelper->getChangeSet($page))) {
                return null;
            }

            if (null === $parent = $cs[0]) {
                return null;
            }
        }

        if ($this->persistenceHelper->isScheduledForRemove($parent)) {
            return $this->getLocalizedParentTranslation($parent, $locale);
        }

        if (!$parent->hasTranslationForLocale($locale)) {
            return $this->getLocalizedParentTranslation($parent, $locale);
        }

        return $parent->translate($locale);
    }

    public function postFlush(): void
    {
        foreach ($this->redirections as $data) {
            $redirectionEvent = new BuildRedirectionEvent($data['from'], $data['to'], true);
            $this->eventDispatcher->dispatch($redirectionEvent, RedirectionEvents::BUILD);
        }

        $this->redirections = [];
    }

    protected function getTranslationFromEvent(ResourceEventInterface $event): PageTranslationInterface
    {
        $resource = $event->getResource();

        if (!$resource instanceof PageTranslationInterface) {
            throw new UnexpectedTypeException($resource, PageTranslationInterface::class);
        }

        return $resource;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PageEvents::TRANSLATION_UPDATE => 'onUpdate',
            PageEvents::TRANSLATION_DELETE => 'onDelete',
        ];
    }
}
