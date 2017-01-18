<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Editor\Exception;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Bundle\CmsBundle\Editor\View;
use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use Ekyna\Bundle\CmsBundle\Model as CM;
use Ekyna\Bundle\CoreBundle\Cache\TagManager;

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
     * @var TagManager
     */
    protected $tagManager;

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
     * @param TagManager             $tagManager
     * @param array                  $config
     */
    public function __construct(
        EntityManagerInterface $manager,
        Editor $editor,
        PageHelper $pageHelper,
        TagManager $tagManager,
        array $config
    ) {
        $this->manager = $manager;
        $this->editor = $editor;
        $this->pageHelper = $pageHelper;
        $this->tagManager = $tagManager;

        $this->config = array_replace($this->getDefaultConfig(), $config);
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('cms_document_data', [$this, 'renderDocumentData'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_content', [$this, 'renderContent'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_container', [$this, 'renderContainer'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_row', [$this, 'renderRow'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_block', [$this, 'renderBlock'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @inheritdoc
     */
    public function initRuntime(\Twig_Environment $twig)
    {
        // TODO Need a CmsRenderer

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
     * @param CM\ContentSubjectInterface|EM\ContentInterface|View\ContentView|null $content
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
                    if ($page->isAdvanced()) {
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
        } elseif ($content instanceof CM\ContentSubjectInterface) {
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

        if ($content instanceof EM\ContentInterface) {
            $this->tagManager->addTags($content->getEntityTag());

            $content = $this->editor->getViewBuilder()->buildContent($content);
        }

        if (!$content instanceof View\ContentView) {
            throw new Exception\InvalidArgumentException(
                'Expected string or instance of ' . CM\ContentSubjectInterface::class . ', ' .
                EM\ContentInterface::class . ' or ' . View\ContentView::class
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
     * @param string|EM\ContainerInterface|View\ContainerView $container
     * @param string                                          $type
     * @param array                                           $data
     *
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function renderContainer($container, $type = null, array $data = [])
    {
        if (is_string($container)) {
            if (null === $element = $this->editor->getRepository()->findContainerByName($container)) {
                $this->persist($element = $this->editor->getContainerManager()->create($container, $type, $data));
            }
            $container = $element;
        }

        if ($container instanceof EM\ContainerInterface) {
            $this->tagManager->addTags($container->getEntityTag());

            $container = $this->editor->getViewBuilder()->buildContainer($container);
        }

        if (!$container instanceof View\ContainerView) {
            throw new Exception\InvalidArgumentException(
                'Expected string or instance of ' .
                EM\ContainerInterface::class . ' or ' .
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
     * @param EM\RowInterface|View\RowView $row
     * @param array                        $data
     *
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function renderRow($row, array $data = [])
    {
        if (is_string($row)) {
            if (null === $element = $this->editor->getRepository()->findRowByName($row)) {
                $this->persist($element = $this->editor->getRowManager()->create($row, $data));
            }
            $row = $element;
        }

        if ($row instanceof EM\RowInterface) {
            $this->tagManager->addTags($row->getEntityTag());

            $row = $this->editor->getViewBuilder()->buildRow($row);
        }

        if (!$row instanceof View\RowView) {
            throw new Exception\InvalidArgumentException(
                'Expected string or instance of ' .
                EM\RowInterface::class . ' or ' .
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
     * @param EM\BlockInterface|View\BlockView $block
     * @param string                           $type
     * @param array                            $data
     *
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function renderBlock($block, $type = null, array $data = [])
    {
        if (is_string($block)) {
            if (null === $element = $this->editor->getRepository()->findBlockByName($block)) {
                $this->persist($element = $this->editor->getBlockManager()->create($block, $type, $data));
            }
            $block = $element;
        }

        if ($block instanceof EM\BlockInterface) {
            $this->tagManager->addTags($block->getEntityTag());

            $block = $this->editor->getViewBuilder()->buildBlock($block);
        }

        if (!$block instanceof View\BlockView) {
            throw new Exception\InvalidArgumentException(
                'Expected string or instance of ' .
                EM\BlockInterface::class . ' or ' .
                View\BlockView::class
            );
        }

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->template->renderBlock('cms_block', [
            'block' => $block,
        ]);
    }

    /**
     * @inheritdoc
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
