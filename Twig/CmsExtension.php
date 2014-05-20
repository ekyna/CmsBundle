<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;

/**
 * CmsExtension
 * 
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsExtension extends \Twig_Extension
{
    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var \Twig_Template
     */
    protected $contentTemplate;

    /**
     * @var boolean
     */
    protected $contentEnabled;

    /**
     * Constructor
     * 
     * @param PageRepository $pageRepository
     * @param RequestStack   $requestStack
     */
    public function __construct(
        PageRepository $pageRepository,
        RequestStack $requestStack,
        \Twig_Environment $environment,
        $template = 'EkynaCmsBundle:Cms:content.html.twig',
        $contentEnabled = false
    ) {
        $this->pageRepository = $pageRepository;
        $this->requestStack   = $requestStack;
        $this->environment    = $environment;
        $this->template       = $template;
        $this->contentEnabled = $contentEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'cms_metas'   => new \Twig_Function_Method($this, 'renderMetas',   array('is_safe' => array('html'))),
            'cms_meta'    => new \Twig_Function_Method($this, 'renderMeta',    array('is_safe' => array('html'))),
            'cms_title'   => new \Twig_Function_Method($this, 'renderTitle',   array('is_safe' => array('html'))),
            'cms_content' => new \Twig_Function_Method($this, 'renderContent', array('is_safe' => array('html'))),
            'cms_block'   => new \Twig_Function_Method($this, 'renderBlock',   array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return array(
            'cms_content_enabled' => $this->contentEnabled,
        );
    }

    /**
     * Returns the current page.
     * 
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    private function getCurrentPage()
    {
        if (null !== $request = $this->requestStack->getCurrentRequest()) {
            return $this->pageRepository->findOneBy(array('route' => $request->attributes->get('_route')));
        }
        return null;
    }

    /**
     * Generates document title and metas tags from the given Seo object or regarding to the current page.
     * 
     * @return string
     */
    public function renderMetas(SeoInterface $seo = null)
    {
        if (null === $seo) {
            if (null !== $page = $this->getCurrentPage()) {
                $seo = $page->getSeo();
            }
        }
        if (null !== $seo) {
            return $this->renderTitle('title', $seo->getTitle()) . $this->renderMeta('description', $seo->getDescription());
        }
        return '<title>Undefined</title>';
    }

    /**
     * Generates a meta tag.
     * 
     * @return string
     */
    public function renderMeta($name, $content)
    {
        return sprintf('<meta name="%s" content="%s">', $name, $content);
    }

    /**
     * Returns current page's title.
     * 
     * @param string $tag
     * @param string $content
     * 
     * @return string
     */
    public function renderTitle($tag = 'h1', $content = 'Undefined title')
    {
        if (null !== $page = $this->getCurrentPage()) {
            $content = $page->getTitle();
        }
        return sprintf('<%s>%s</%s>', $tag, $content, $tag);
    }

    /**
     * Generates html from given Content.
     * 
     * @param ContentInterface $content
     * 
     * @return string
     */
    public function renderContent(ContentInterface $content = null)
    {
        if (null === $content) {
            if (null !== $page = $this->getCurrentPage()) {
                if(null === $content = $page->getContent()) {
                    if(0 < strlen($html = $page->getHtml())) {
                        return $html;
                    }
                }
            }
        }

        if(null === $content) {
            return '<p>Page en construction.</p>';
        }

        if(!$this->template instanceOf \Twig_Template) {
            $this->template = $this->environment->loadTemplate($this->template);
        }

        return $this->template->renderBlock('cms_content', array('content' => $content));
    }

    /**
     * Generates html from given Block.
     * 
     * @param BlockInterface $block
     * 
     * @return string
     */
    public function renderBlock(BlockInterface $block)
    {
        $token = sprintf('cms_block_%s', $block->getType());
        if(!$this->template->hasBlock($token)) {
            throw new \InvalidArgumentException('Unable to find "%s" twig block.', $token);
        }

        return trim($this->template->renderBlock($token, array('block' => $block)));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
    	return 'ekyna_cms';
    }
}
