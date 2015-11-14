<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Model\PageTranslationInterface;
use Ekyna\Bundle\SettingBundle\Event\BuildRedirectionEvent;
use Ekyna\Bundle\SettingBundle\Event\RedirectionEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PageTranslationListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageTranslationListener implements EventSubscriber
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $redirections = [];


    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * On flush event handler.
     *
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        // Handle update pages paths.
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof PageTranslationInterface) {
                $changeSet = $uow->getEntityChangeSet($entity);
                if (array_key_exists('path', $changeSet)) {
                    $locale = $entity->getLocale();
                    // TODO use url generator or i18n routing prefix strategy
                    $localePrefix = $locale != 'fr' ? '/' . $locale : '';
                    $this->redirections[] = array(
                        'from' => $from = $localePrefix . $changeSet['path'][0],
                        'to'   => $to = $localePrefix . $changeSet['path'][1],
                    );

                    /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $page */
                    $page = $entity->getTranslatable();
                    // Update the children pages translations paths.
                    if ($page->hasChildren()) {
                        $metadata = $em->getClassMetadata(get_class($entity));
                        $this->updateChildrenPageTranslationPath(
                            $page, $uow, $metadata, $from, $to, $locale
                        );
                    }
                }
            }
        }

        // Handle deleted pages paths.
        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof PageTranslationInterface) {
                $from = $entity->getPath();
                $locale = $entity->getLocale();
                // TODO use url generator or i18n routing prefix strategy
                $localePrefix = $locale != 'fr' ? '/' . $locale : '';

                /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $parentPage */
                $parentPage = $entity->getTranslatable();
                while (null !== $parentPage = $parentPage->getParent()) {
                    // Look for a non-deleted parent page.
                    if (!$uow->isScheduledForDelete($parentPage)) {
                        // Redirect to this parent page.
                        $to = $parentPage->translate($locale)->getPath();
                        if (0 < strlen($to)) {
                            $redirection[] = array(
                                'from' => $localePrefix . $from,
                                'to'   => $localePrefix . $to,
                            );
                        }
                        break;
                    }
                }
            }
        }
    }

    /**
     * Updates the page children translation path recursively.
     *
     * @param PageInterface $page
     * @param UnitOfWork    $uow
     * @param ClassMetadata $metadata
     * @param string        $from
     * @param string        $to
     * @param string        $locale
     */
    private function updateChildrenPageTranslationPath(
        PageInterface $page,
        UnitOfWork $uow,
        ClassMetadata $metadata,
        $from,
        $to,
        $locale
    ) {
        // TODO use url generator or i18n routing prefix strategy
        $localePrefix = $locale != 'fr' ? '/' . $locale : '';
        foreach ($page->getChildren() as $child) {
            $translation = $child->translate($locale);
            $oldPath = $translation->getPath();
            if (0 === strpos($oldPath, $from)) {
                $newPath = str_replace($from, $to, $oldPath);
                $translation->setPath($newPath);
                $uow->recomputeSingleEntityChangeSet($metadata, $translation);

                $this->redirections[] = array(
                    'from' => $localePrefix. $oldPath,
                    'to'   => $localePrefix. $newPath,
                );

                // Update the children pages translations paths.
                if ($child->hasChildren()) {
                    $this->updateChildrenPageTranslationPath($child, $uow, $metadata, $oldPath, $newPath, $locale);
                }
            }
        }
    }

    /**
     * Post flush event handler.
     */
    public function postFlush()
    {
        foreach ($this->redirections as $redirection) {
            $redirectionEvent = new BuildRedirectionEvent($redirection['from'], $redirection['to'], true);
            $this->dispatcher->dispatch(RedirectionEvents::BUILD, $redirectionEvent);
        }

        $this->redirections = [];
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush,
            Events::postFlush,
        );
    }
}
