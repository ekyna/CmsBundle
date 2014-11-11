<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;

/**
 * Class CmsExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsExtension extends \Twig_Extension
{
    /**
     * @var Editor
     */
    protected $editor;

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
     * @param Editor $editor
     * @param array $config
     */
    public function __construct(Editor $editor, array $config = array())
    {
        $this->editor = $editor;

        $this->config = array_merge(array(
            'template' => 'EkynaCmsBundle:Editor:content.html.twig',
            'seo_no_follow' => true,
            'seo_no_index' => true,
        ), $config);
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $twig)
    {
        $this->template = $twig->loadTemplate($this->config['template']);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cms_metas', array($this, 'renderMetas'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_meta', array($this, 'renderMeta'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_title', array($this, 'renderTitle'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_content', array($this, 'renderContent'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_content_block', array($this, 'renderContentBlock'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_block', array($this, 'renderBlock'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Generates document title and metas tags from the given Seo object or regarding to the current page.
     *
     * @param SeoInterface $seo
     * @return string
     */
    public function renderMetas(SeoInterface $seo = null)
    {
        if (null === $seo) {
            if (null !== $page = $this->editor->getCurrentPage()) {
                $seo = $page->getSeo();
            }
        }

        if (null !== $seo) {
            $follow = !$this->config['seo_no_follow'] ? ($seo->getFollow() ? 'follow' : 'nofollow') : 'nofollow';
            $index = !$this->config['seo_no_index'] ? ($seo->getIndex() ?  'index'  : 'noindex') : 'noindex';
            $robots = sprintf('%s,%s', $follow, $index);
            $metas =
                $this->renderTitle('title', $seo->getTitle()) . "\n" .
                $this->renderMeta('description', $seo->getDescription()) . "\n" .
                $this->renderMeta('robots', $robots)
            ;
            if (0 < strlen($canonical = $seo->getCanonical())) {
                $metas .= "\n" .$this->renderTag('link', null, array(
                    'rel' => 'canonical',
                    'href' => $canonical,
                ));
            }
        } else {
            $metas = '<title>Undefined</title>' . $this->renderMeta('robots', 'follow,noindex');
        }

        return $metas;
    }

    /**
     * Generates a meta tag.
     *
     * @param string $name
     * @param string $content
     *
     * @return string
     */
    public function renderMeta($name, $content)
    {
        return $this->renderTag('meta', null, array('name' => $name, 'content' => $content));
    }

    /**
     * Renders the html tag.
     *
     * @param $tag
     * @param string $content
     * @param array $attributes
     *
     * @return string
     */
    private function renderTag($tag, $content = null, array $attributes = array())
    {
        $attr = [];
        foreach($attributes as $key => $value) {
            $attr[] = sprintf(' %s="%s"', $key, $value);
        }
        if (0 < strlen($content)) {
            return sprintf('<%s%s>%s</%s>', $tag, implode('', $attr), $content, $tag);
        } else {
            return sprintf('<%s%s />', $tag, implode('', $attr));
        }
    }

    /**
     * Returns current page's title.
     *
     * @param string $tag
     * @param string $content
     *
     * @return string
     */
    public function renderTitle($tag = 'h1', $content = null)
    {
        if (null === $content && null !== $page = $this->editor->getCurrentPage()) {
            $content = $page->getTitle();
        }
        if (0 == strlen($content)) {
            $content = 'Undefined title';
        }
        return sprintf('<%s>%s</%s>', $tag, $content, $tag);
    }

    /**
     * Generates html from given Content.
     *
     * @param mixed $subject
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderContent($subject = null)
    {
        $content = null;

        if ($subject instanceOf ContentInterface) {
            $content = $subject;
        } elseif ($subject instanceof ContentSubjectInterface) {
            if (null === $content = $subject->getContent()) {
                $content = $this->editor->createDefaultContent($subject);
            }
        } elseif (null === $subject) {
            if (null !== $page = $this->editor->getCurrentPage()) {
                if (null === $content = $page->getContent()) {
                    if ($page->getAdvanced()) {
                        $content = $this->editor->createDefaultContent($page);
                    } elseif (0 < strlen($html = $page->getHtml())) {
                        return $html;
                    } else {
                        return '<p>Page en construction.</p>';
                    }
                }
            }
        }

        if (null === $content) {
            throw new \RuntimeException('Undefined content.');
        }

        // TODO fix : no template inheritance with this method.
        if (!$this->template->hasBlock('cms_block_content')) {
            throw new \RuntimeException('Unable to find "cms_block_content" twig block.');
        }

        return $this->template->renderBlock('cms_block_content', array(
            'content' => $content,
            'editable' => $this->editor->isEnabled()
        ));
    }

    /**
     * Generates html from given Content Block.
     *
     * @param BlockInterface $block
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderContentBlock(BlockInterface $block)
    {
        $blockName = sprintf('cms_block_%s', $block->getType());
        if (!$this->template->hasBlock($blockName)) {
            throw new \RuntimeException('Unable to find "%s" twig block.', $blockName);
        }
        $this->editor->setRenderedBlocks();
        return trim($this->template->renderBlock($blockName, array('block' => $block)));
    }

    /**
     * Generates html from given Block.
     *
     * @param string $name the block name
     * @param string $type the block type
     * @param array $datas the block datas
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderBlock($name, $type = null, array $datas = array())
    {
        $block = $this->editor->findBlockByName($name, $type, $datas);
        $this->editor->setRenderedBlocks();
        return $this->template->renderBlock('cms_block', array(
            'block' => $block,
            'editable' => $this->editor->isEnabled()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms';
    }
}
