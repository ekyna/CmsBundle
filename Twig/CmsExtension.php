<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;

/**
 * CmsExtension
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
        $this->requestStack = $requestStack;
        $this->environment = $environment;
        $this->template = $template;
        $this->contentEnabled = $contentEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'ekyna_cms_metas' => new \Twig_Function_Method($this, 'renderMetas', array('is_safe' => array('html'))),
            'cms_content' => new \Twig_Function_Method($this, 'renderContent', array('is_safe' => array('html'))),
            'cms_block' => new \Twig_Function_Method($this, 'renderBlock', array('is_safe' => array('html'))),
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
     * Returns the current page
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
     * Generates document title and metas tags regarding to current request
     * 
     * @return string
     */
    public function renderMetas()
    {
        $output = '<title>Undefined</title>';
        if (null !== $page = $this->getCurrentPage()) {
            $seo = $page->getSeo();
            $output = sprintf('<title>%s</title>', $seo->getTitle()) . "\n";
            $output .= sprintf('<meta name="description" content="%s">', $seo->getDescription());
        }
        return $output;
    }

    /**
     * Generates html from given Content
     * 
     * @param ContentInterface $content
     * 
     * @return string
     */
    public function renderContent(ContentInterface $content = null)
    {
        if (null === $content) {
            if (null !== $page = $this->getCurrentPage()) {
                $content = $page->getContent();
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
     * Generates html from given Block
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
