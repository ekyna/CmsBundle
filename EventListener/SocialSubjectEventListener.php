<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\SocialButtonsBundle\Event\SubjectEvent;
use Ekyna\Bundle\SocialButtonsBundle\Event\SubjectEvents;
use Ekyna\Bundle\SocialButtonsBundle\Share\Subject;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class SocialSubjectEventListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class SocialSubjectEventListener implements EventSubscriberInterface
{
    /**
     * @var PageHelper
     */
    private $pageHelper;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;


    /**
     * Constructor.
     *
     * @param PageHelper               $pageHelper
     * @param UrlGeneratorInterface    $urlGenerator
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
    public function onResolveSubject(SubjectEvent $event)
    {
        $params = $event->getParameters();

        if (array_key_exists('type', $params)) {
            if ($params['type'] == 'page') {
                $event->setSubject($this->createPageSubject());
                $event->stopPropagation();
            } elseif ($params['type'] == 'global') {
                $event->setSubject($this->createGlobalSubject());
                $event->stopPropagation();
            }
        }
    }

    /**
     * Creates the current page social share subject.
     *
     * @return Subject|null
     */
    private function createPageSubject()
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
    private function createGlobalSubject()
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
     * @return Subject|null
     */
    private function createSubjectFromPage(PageInterface $page)
    {
        try {
            $url = $this->urlGenerator->generate($page->getRoute(), array(), true);

            $subject = new Subject();
            $subject->title = $page->getSeo()->getTitle();
            $subject->url   = $url;

            return $subject;
        } catch (ExceptionInterface $e) {
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            SubjectEvents::RESOLVE => array('onResolveSubject', 0),
        );
    }
}
