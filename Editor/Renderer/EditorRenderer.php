<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Renderer;

use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Editor\Exception;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Bundle\CmsBundle\Editor\View;
use Ekyna\Bundle\CmsBundle\Model as CM;
use Ekyna\Bundle\CmsBundle\Service\Helper\PageHelper;
use Ekyna\Bundle\ResourceBundle\Service\Http\TagManager;
use Twig\Environment;
use Twig\Error\Error;
use Twig\TemplateWrapper;

/**
 * Class EditorRenderer
 * @package Ekyna\Bundle\CmsBundle\Editor\Renderer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class EditorRenderer
{
    protected array $config;

    private ?TemplateWrapper $template = null;

    public function __construct(
        protected readonly Editor $editor,
        protected readonly PageHelper $pageHelper,
        protected readonly TagManager $tagManager,
        protected readonly Environment $environment,
        protected readonly EntityManagerInterface $manager,
        array     $config = []
    ) {
        $this->config = array_replace([
            'template'        => '@EkynaCms/Editor/content.html.twig',
            'default_content' => '<p></p>', // TODO
        ], $config);
    }

    /**
     * Renders the document data attribute.
     */
    public function renderDocumentData(): string
    {
        if (!$this->editor->isEnabled()) {
            return '';
        }

        return " data-cms-editor-document='" . json_encode($this->editor->getContentData()) . "'";
    }

    /**
     * Renders the content.
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RenderingException
     */
    public function renderContent(
        CM\ContentSubjectInterface|EM\ContentInterface|View\ContentView|string|null $content = null
    ): string {
        $repository = $this->editor->getRepository();

        if (null === $content) {
            if (null !== $page = $this->pageHelper->getCurrent()) {
                if (null === $content = $repository->loadSubjectContent($page)) {
                    if ($page->isAdvanced()) {
                        $content = $this->editor->createDefaultContent($page);
                        $this->persist($page);
                    } elseif (!empty($html = $page->getHtml())) {
                        return $html;
                    } else {
                        return $this->config['default_content'];
                    }
                }
            } else {
                throw new Exception\RenderingException('Undefined content.');
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

        return $this->getTemplate()->renderBlock('cms_content', [
            'content' => $content,
        ]);
    }

    /**
     * Renders the container.
     *
     * @throws Exception\InvalidArgumentException
     */
    public function renderContainer(
        EM\ContainerInterface|View\ContainerView|string $container,
        string                                          $type = null,
        array                                           $data = []
    ): string {
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

        return $this->getTemplate()->renderBlock('cms_container', [
            'container' => $container,
        ]);
    }

    /**
     * Renders the row.
     *
     * @throws Exception\InvalidArgumentException
     */
    public function renderRow(EM\RowInterface|View\RowView|string $row, array $data = []): string
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

        return $this->getTemplate()->renderBlock('cms_row', [
            'row' => $row,
        ]);
    }

    /**
     * Renders the block.
     *
     * @throws Exception\InvalidArgumentException
     */
    public function renderBlock(
        EM\BlockInterface|View\BlockView|string $block,
        string                                  $type = null,
        array                                   $data = []
    ): string {
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

        return $this->getTemplate()->renderBlock('cms_block', [
            'block' => $block,
        ]);
    }

    /**
     * @return TemplateWrapper
     *
     * @throws Exception\RenderingException
     */
    private function getTemplate(): TemplateWrapper
    {
        if ($this->template) {
            return $this->template;
        }

        try {
            $this->template = $this->environment->load($name = $this->config['template']);
        } catch (Error) {
            throw new Exception\RenderingException("Failed to load $name template.");
        }

        // TODO : hasBlock() does not use template inheritance.

        if (!$this->template->hasBlock('cms_content')) {
            throw new Exception\RenderingException('Unable to find "cms_content" twig block.');
        }
        if (!$this->template->hasBlock('cms_container')) {
            throw new Exception\RenderingException('Unable to find "cms_container" twig block.');
        }
        if (!$this->template->hasBlock('cms_row')) {
            throw new Exception\RenderingException('Unable to find "cms_row" twig block.');
        }
        if (!$this->template->hasBlock('cms_block')) {
            throw new Exception\RenderingException('Unable to find "cms_block" twig block.');
        }

        return $this->template;
    }

    /**
     * Persists the element and flushes the manager.
     */
    private function persist(object $element): void
    {
        $this->manager->persist($element);
        $this->manager->flush();
    }
}
