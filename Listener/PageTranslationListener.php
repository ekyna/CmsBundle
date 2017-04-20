<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
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
 *
 * @todo resource (persistence) event subscriber
 */
class PageTranslationListener
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
     * Pre update event handler.
     *
     * @param PageTranslationInterface $translation
     * @param PreUpdateEventArgs       $eventArgs
     */
    public function preUpdate(PageTranslationInterface $translation, PreUpdateEventArgs $eventArgs)
    {
        if ($eventArgs->hasChangedField('path')) {
            $em = $eventArgs->getEntityManager();
            $uow = $em->getUnitOfWork();

            $locale = $translation->getLocale();
            $from = $eventArgs->getOldValue('path');
            $to   = $eventArgs->getNewValue('path');

            if (!empty($from) && !empty($to) && $from != $to) {
                // TODO use url generator or i18n routing prefix strategy
                $localePrefix = $locale != 'fr' ? '/' . $locale : '';
                $this->redirections[] = array(
                    'from' => $localePrefix . $from,
                    'to'   => $localePrefix . $to,
                );
            }

            /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $page */
            $page = $translation->getTranslatable();
            // Update the children pages translations paths.
            if ($page->hasChildren()) {
                $metadata = $em->getClassMetadata(get_class($translation));
                $this->updateChildrenPageTranslationPath(
                    $page, $uow, $metadata, $from, $to, $locale
                );
            }
        }
    }

    /**
     * Pre remove event handler.
     *
     * @param PageTranslationInterface $translation
     */
    public function preRemove(PageTranslationInterface $translation)
    {
        /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $translatable */
        $translatable = $translation->getTranslatable();
        if (null !== $parentPage = $translatable->getParent()) {
            $from = $translation->getPath();
            $locale = $translation->getLocale();
            // TODO use url generator or i18n routing prefix strategy
            $localePrefix = $locale != 'fr' ? '/' . $locale : '';

            // Redirect to this parent page.
            $to = $parentPage->translate($locale)->getPath();
            if (!empty($from) && !empty($to) && $from != $to) {
                $this->redirections[] = array(
                    'from' => $localePrefix . $from,
                    'to'   => $localePrefix . $to,
                );
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

                if (!empty($oldPath) && !empty($newPath) && $oldPath != $newPath) {
                    $this->redirections[] = array(
                        'from' => $localePrefix . $oldPath,
                        'to'   => $localePrefix . $newPath,
                    );
                }

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
            $this->dispatcher->dispatch($redirectionEvent, RedirectionEvents::BUILD);
        }

        $this->redirections = [];
    }
}
