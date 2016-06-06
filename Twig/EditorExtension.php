<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Editor\View;
use Ekyna\Bundle\CmsBundle\Editor\ViewBuilder;
use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Class EditorExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class EditorExtension extends \Twig_Extension
{
    /**
     * @var Editor
     */
    protected $editor;

    /**
     * @var PageHelper
     */
    protected $pageHelper;

    /**
     * @var ViewBuilder
     */
    protected $viewBuilder;

    /**
     * @var \Twig_Template
     */
    protected $template;


    /**
     * Constructor.
     *
     * @param Editor      $editor
     * @param PageHelper  $pageHelper
     * @param ViewBuilder $viewBuilder
     */
    public function __construct(Editor $editor, PageHelper $pageHelper, ViewBuilder $viewBuilder)
    {
        $this->editor = $editor;
        $this->pageHelper = $pageHelper;
        $this->viewBuilder = $viewBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('cms_content', [$this, 'renderContent'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_container', [$this, 'renderContainer'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_block', [$this, 'renderBlock'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_static_container', [$this, 'renderStaticContainer'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_static_block', [$this, 'renderStaticBlock'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $twig)
    {
        $this->template = $twig->loadTemplate('EkynaCmsBundle:Editor:content.html.twig'); // TODO config

        // TODO : hasBlock() does not use template inheritance.

        /** @noinspection PhpInternalEntityUsedInspection */
        if (!$this->template->hasBlock('cms_content')) {
            throw new \RuntimeException('Unable to find "cms_content" twig block.');
        }
        /** @noinspection PhpInternalEntityUsedInspection */
        if (!$this->template->hasBlock('cms_container')) {
            throw new \RuntimeException('Unable to find "cms_container" twig block.');
        }
        /** @noinspection PhpInternalEntityUsedInspection */
        if (!$this->template->hasBlock('cms_block')) {
            throw new \RuntimeException('Unable to find "cms_block" twig block.');
        }
    }

    /**
     * Renders the content.
     *
     * @param Model\ContentSubjectInterface|Model\ContentInterface|View\Content|null $subjectOrContentOrView
     *
     * @return string
     */
    public function renderContent($subjectOrContentOrView = null)
    {
        if (null === $subjectOrContentOrView) {
            if (null !== $page = $this->pageHelper->getCurrent()) {
                if (null !== $content = $page->getContent()) {
                    $subjectOrContentOrView = $content;
                } else {
                    if ($page->getAdvanced()) {
                        $subjectOrContentOrView = $this->editor->createDefaultContent($page);
                    } elseif (0 < strlen($html = $page->getHtml())) {
                        return $html;
                    } else {
                        return '<p></p>'; // TODO default content
                    }
                }
            } else {
                throw new \RuntimeException('Undefined content.');
            }
        }
        if ($subjectOrContentOrView instanceof Model\ContentSubjectInterface) {
            $subjectOrContentOrView = $subjectOrContentOrView->getContent();
        }
        if ($subjectOrContentOrView instanceof Model\ContentInterface) {
            // TODO Tag response as Content relative
            /*if (null !== $subjectOrContentOrView->getId()) {
                $this->tagManager->addTags($subjectOrContentOrView->getEntityTag());
            }*/
            $subjectOrContentOrView = $this->viewBuilder->buildContent($subjectOrContentOrView);
        }
        if (!$subjectOrContentOrView instanceof View\Content) {
            throw new \InvalidArgumentException(
                'Expected instance of ' . Model\ContentSubjectInterface::class . ', ' .
                Model\ContentInterface::class . ' or ' . View\Content::class
            );
        }

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->template->renderBlock('cms_content', [
            'content' => $subjectOrContentOrView,
        ]);
    }

    /**
     * Renders the container.
     *
     * @param Model\ContainerInterface|View\Container $containerOrView
     *
     * @return string
     */
    public function renderContainer($containerOrView)
    {
        if ($containerOrView instanceof Model\ContainerInterface) {
            // TODO Tags the response as Container relative
            //$this->tagManager->addTags($containerOrView->getEntityTag());

            $containerOrView = $this->viewBuilder->buildContainer($containerOrView);
        }

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->template->renderBlock('cms_container', [
            'container' => $containerOrView,
        ]);
    }

    /**
     * Renders the static container.
     *
     * @param string $name
     * @param null $data
     *
     * @return string
     */
    public function renderStaticContainer($name, $data = null)
    {
        // TODO
        return '<p>[TODO]</p>';
    }

    /**
     * Renders the block.
     *
     * @param Model\BlockInterface|View\Block $blockOrView
     *
     * @return string
     */
    public function renderBlock($blockOrView)
    {
        if ($blockOrView instanceof Model\BlockInterface) {
            // TODO Tags the response as Block relative
            //$this->tagManager->addTags($blockOrView->getEntityTag());

            $blockOrView = $this->viewBuilder->buildBlock($blockOrView);
        }

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->template->renderBlock('cms_block', [
            'block' => $blockOrView,
        ]);
    }

    /**
     * Renders the static block.
     *
     * @param string $name
     * @param string $type
     * @param mixed  $data
     *
     * @return string
     */
    public function renderStaticBlock($name, $type = 'ekyna_cms_tinymce', $data = null)
    {
        // TODO
        return '<p>[TODO]</p>';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_editor';
    }
}
