<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * CmsExtension.
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
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Twig_Template
     */
    protected $template;


    /**
     * Constructor
     * 
     * @param PageRepository $pageRepository
     * @param RequestStack   $requestStack
     */
    public function __construct(PageRepository $pageRepository, RequestStack $requestStack, SecurityContext $securityContext, array $config = array())
    {
        $this->pageRepository = $pageRepository;
        $this->requestStack   = $requestStack;
        $this->securityContext = $securityContext;
        $this->config = array_merge(array(
        	'template' => 'EkynaCmsBundle:Cms:content.html.twig',
            'enabled'  => true,
        ), $config);
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
     * {@inheritDoc}
     */
	public function initRuntime(\Twig_Environment $environment)
	{
        $this->template = $environment->loadTemplate($this->config['template']);
	}

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return array(
            'cms_content_enabled' => $this->config['enabled'],
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
    public function renderTitle($tag = 'h1', $content = null)
    {
        if (null === $content && null !== $page = $this->getCurrentPage()) {
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
     * @param ContentInterface $content
     * 
     * @throws \RuntimeException
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

        if (null === $content) {
            return '<p>Page en construction.</p>';
        }

        if (! $this->template->hasBlock('cms_block_content')) {
            throw new \RuntimeException('Unable to find "cms_block_content" twig block.');
        }

        $editable = $this->securityContext->isGranted('ROLE_ADMIN');
        if ($editable && null !== $request = $this->requestStack->getCurrentRequest()) {
            $request->headers->set('X-CmsEditor-Injection', true);
        }

        return $this->template->renderBlock('cms_block_content', array('content' => $content, 'editable' => $editable));
    }

    /**
     * Generates html from given Block.
     * 
     * @param BlockInterface $block
     * 
     * @throws \RuntimeException
     * 
     * @return string
     */
    public function renderBlock(BlockInterface $block)
    {
        $token = sprintf('cms_block_%s', $block->getType());
        if(!$this->template->hasBlock($token)) {
            throw new \RuntimeException('Unable to find "%s" twig block.', $token);
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
