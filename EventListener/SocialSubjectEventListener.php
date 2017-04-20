<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Service\Helper\PageHelper;
use Ekyna\Bundle\SocialButtonsBundle\Event\SubjectEvent;
use Ekyna\Bundle\SocialButtonsBundle\Event\SubjectEvents;
use Ekyna\Bundle\SocialButtonsBundle\Model\Subject;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class SocialSubjectEventListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SocialSubjectEventListener implements EventSubscriberInterface
{
    private PageHelper            $pageHelper;
    private UrlGeneratorInterface $urlGenerator;


    /**
     * Constructor.
     *
     * @param PageHelper            $pageHelper
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(PageHelper $pageHelper, UrlGeneratorInterface $urlGenerator)
    {
        $this->pageHelper = $pageHelper;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Resolve subject event handler.
     *
     * @param SubjectEvent $event
     */
    public function onResolveSubject(SubjectEvent $event): void
    {
        $params = $event->getParameters();

        if (!array_key_exists('type', $params)) {
            return;
        }

        if ($params['type'] === 'page') {
            $event->setSubject($this->createPageSubject());
            $event->stopPropagation();

            return;
        }

        if ($params['type'] === 'global') {
            $event->setSubject($this->createGlobalSubject());
            $event->stopPropagation();
        }
    }

    /**
     * Creates the current page social share subject.
     *
     * @return Subject|null
     */
    private function createPageSubject(): ?Subject
    {
        if (null !== $page = $this->pageHelper->getCurrent()) {
            return $this->createSubjectFromPage($page);
        }

        return null;
    }

    /**
     * Creates the global social share subject.
     *
     * @return Subject|null
     */
    private function createGlobalSubject(): ?Subject
    {
        if (null !== $home = $this->pageHelper->getHomePage()) {
            return $this->createSubjectFromPage($home);
        }

        return null;
    }

    /**
     * Creates the social share subject from the given page.
     *
     * @param PageInterface $page
     *
     * @return Subject|null
     */
    private function createSubjectFromPage(PageInterface $page): ?Subject
    {
        try {
            $url = $this->urlGenerator->generate($page->getRoute(), [], UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (ExceptionInterface $e) {
            return null;
        }

        $subject = new Subject();
        $subject->title = $page->getSeo()->getTitle();
        $subject->url = $url;

        return $subject;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SubjectEvents::RESOLVE => ['onResolveSubject', 0],
        ];
    }
}
