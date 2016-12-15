<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Editor\Exception;
use Ekyna\Bundle\CmsBundle\Editor\View;
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
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * @var Editor
     */
    protected $editor;

    /**
     * @var PageHelper
     */
    protected $pageHelper;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Twig_Template
     */
    protected $template;


    /**
     * Constructor.
     *
     * @param EntityManagerInterface $manager
     * @param Editor                 $editor
     * @param PageHelper             $pageHelper
     * @param array                  $config
     */
    public function __construct(
        EntityManagerInterface $manager,
        Editor $editor,
        PageHelper $pageHelper,
        array $config
    ) {
        $this->manager = $manager;
        $this->editor = $editor;
        $this->pageHelper = $pageHelper;

        $this->config = array_replace($this->getDefaultConfig(), $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('cms_document_data', [$this, 'renderDocumentData'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_content',       [$this, 'renderContent'],      ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_container',     [$this, 'renderContainer'],    ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_row',           [$this, 'renderRow'],          ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_block',         [$this, 'renderBlock'],        ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $twig)
    {
        $this->template = $twig->loadTemplate($this->config['template']);

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
        if (!$this->template->hasBlock('cms_row')) {
            throw new \RuntimeException('Unable to find "cms_row" twig block.');
        }
        /** @noinspection PhpInternalEntityUsedInspection */
        if (!$this->template->hasBlock('cms_block')) {
            throw new \RuntimeException('Unable to find "cms_block" twig block.');
        }
    }

    /**
     * Renders the document data attribute.
     *
     * @return string
     */
    public function renderDocumentData()
    {
        if (!$this->editor->isEnabled()) {
            return '';
        }

        return " data-cms-editor-document='" . json_encode($this->editor->getContentData()) . "'";
    }

    /**
     * Renders the content.
     *
     * @param Model\ContentSubjectInterface|Model\ContentInterface|View\ContentView|null $content
     *
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function renderContent($content = null)
    {
        $repository = $this->editor->getRepository();

        if (null === $content) {
            if (null !== $page = $this->pageHelper->getCurrent()) {
                if (null === $content = $repository->loadSubjectContent($page)) {
                    if ($page->getAdvanced()) {
                        $content = $this->editor->createDefaultContent($page);
                        $this->persist($page);
                    } elseif (0 < strlen($html = $page->getHtml())) {
                        return $html;
                    } else {
                        return '<p></p>'; // TODO default content
                    }
                }
            } else {
                throw new \RuntimeException('Undefined content.');
            }
        } elseif ($content instanceof Model\ContentSubjectInterface) {
            if (null === $element = $repository->loadSubjectContent($content)) {
                $element = $this->editor->createDefaultContent($content);
                $this->persist($content);
            }
            $content = $element;
        } elseif (is_string($content)) {
            if (null === $element = $this->editor->getRepository()->findContentByName($content)) {
                $this->persist($element = $this->editor->createDefaultContent($content));
            }
            $content = $element;
        }

        if ($content instanceof Model\ContentInterface) {
            // TODO Tag response as Content relative
            /*if (null !== $subjectOrContentOrView->getId()) {
                $this->tagManager->addTags($subjectOrContentOrView->getEntityTag());
            }*/
            $content = $this->editor->getViewBuilder()->buildContent($content);
        }

        if (!$content instanceof View\ContentView) {
            throw new Exception\InvalidArgumentException(
                'Expected string or instance of ' . Model\ContentSubjectInterface::class . ', ' .
                Model\ContentInterface::class . ' or ' . View\ContentView::class
            );
        }

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->template->renderBlock('cms_content', [
            'content' => $content,
        ]);
    }

    /**
     * Renders the container.
     *
     * @param string|Model\ContainerInterface|View\ContainerView $container
     *
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function renderContainer($container)
    {
        if (is_string($container)) {
            if (null === $element = $this->editor->getRepository()->findContainerByName($container)) {
                $this->persist($element = $this->editor->getContainerManager()->create($container));
            }
            $container = $element;
        }

        if ($container instanceof Model\ContainerInterface) {
            // TODO Tags the response as Container relative
            //$this->tagManager->addTags($containerOrView->getEntityTag());

            $container = $this->editor->getViewBuilder()->buildContainer($container);
        }

        if (!$container instanceof View\ContainerView) {
            throw new Exception\InvalidArgumentException(
                'Expected string or instance of ' .
                Model\ContainerInterface::class . ' or ' .
                View\ContainerView::class
            );
        }

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->template->renderBlock('cms_container', [
            'container' => $container,
        ]);
    }

    /**
     * Renders the row.
     *
     * @param Model\RowInterface|View\RowView $row
     *
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function renderRow($row)
    {
        if (is_string($row)) {
            if (null === $element = $this->editor->getRepository()->findRowByName($row)) {
                $this->persist($element = $this->editor->getRowManager()->create($row));
            }
            $row = $element;
        }

        if ($row instanceof Model\RowInterface) {
            // TODO Tags the response as Row relative
            //$this->tagManager->addTags($containerOrView->getEntityTag());

            $row = $this->editor->getViewBuilder()->buildRow($row);
        }

        if (!$row instanceof View\RowView) {
            throw new Exception\InvalidArgumentException(
                'Expected string or instance of ' .
                Model\RowInterface::class . ' or ' .
                View\RowView::class
            );
        }

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->template->renderBlock('cms_row', [
            'row' => $row,
        ]);
    }

    /**
     * Renders the block.
     *
     * @param Model\BlockInterface|View\BlockView $block
     *
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function renderBlock($block)
    {
        if (is_string($block)) {
            if (null === $element = $this->editor->getRepository()->findBlockByName($block)) {
                $this->persist($element = $this->editor->getBlockManager()->create($block));
            }
            $block = $element;
        }

        if ($block instanceof Model\BlockInterface) {
            // TODO Tags the response as Block relative
            //$this->tagManager->addTags($blockOrView->getEntityTag());

            $block = $this->editor->getViewBuilder()->buildBlock($block);
        }

        if (!$block instanceof View\BlockView) {
            throw new Exception\InvalidArgumentException(
                'Expected string or instance of ' .
                Model\BlockInterface::class . ' or ' .
                View\BlockView::class
            );
        }

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->template->renderBlock('cms_block', [
            'block' => $block,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_editor';
    }

    /**
     * Persists the element and flushes the manager.
     *
     * @param object $element
     */
    private function persist($element)
    {
        $this->manager->persist($element);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->manager->flush($element);
    }

    /**
     * Returns the default config.
     *
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            'template' => 'EkynaCmsBundle:Editor:content.html.twig',
        ];
    }
}
