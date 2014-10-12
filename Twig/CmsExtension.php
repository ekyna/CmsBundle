<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Doctrine\Common\Persistence\ObjectManager;
use Ekyna\Bundle\CmsBundle\Entity\Content;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Ekyna\Bundle\CmsBundle\Editor\PluginRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class CmsExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsExtension extends \Twig_Extension
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var PluginRegistry
     */
    protected $pluginRegistry;

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
     * @param ObjectManager $manager
     * @param RequestStack $requestStack
     * @param SecurityContext $securityContext
     * @param PluginRegistry $pluginRegistry
     * @param array $config
     */
    public function __construct(
        ObjectManager $manager,
        RequestStack $requestStack,
        SecurityContext $securityContext,
        PluginRegistry $pluginRegistry,
        array $config = array()
    )
    {
        $this->manager = $manager;
        $this->requestStack = $requestStack;
        $this->securityContext = $securityContext;
        $this->pluginRegistry = $pluginRegistry;

        $this->config = array_merge(array(
            'template' => 'EkynaCmsBundle:Cms:content.html.twig',
            'default_block_type' => 'tinymce',
        ), $config);
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
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $twig)
    {
        $this->template = $twig->loadTemplate($this->config['template']);
    }

    /**
     * Returns the current page.
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\PageInterface
     */
    private function getCurrentPage()
    {
        if (null !== $request = $this->requestStack->getCurrentRequest()) {
            $repo = $this->manager->getRepository('EkynaCmsBundle:Page');
            return $repo->findOneBy(array('route' => $request->attributes->get('_route')));
        }
        return null;
    }

    /**
     * Returns whether the current user is allowed edit content and blocks or not.
     *
     * @return bool
     */
    private function isEditable()
    {
        if (
            null !== $this->securityContext->getToken()
            && $this->securityContext->isGranted('ROLE_ADMIN')
            && null !== $request = $this->requestStack->getCurrentRequest()
        ) {
            $request->headers->set('X-CmsEditor-Injection', true);
            return true;
        }
        return false;
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
     * @param string $name
     * @param string $content
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
                $content = $this->createDefaultContent($subject);
            }
        } elseif (null === $subject) {
            if (null !== $page = $this->getCurrentPage()) {
                if (null === $content = $page->getContent()) {
                    if ($page->getAdvanced()) {
                        $content = $this->createDefaultContent($page);
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

        if (!$this->template->hasBlock('cms_block_content')) {
            throw new \RuntimeException('Unable to find "cms_block_content" twig block.');
        }

        return $this->template->renderBlock('cms_block_content', array(
            'content' => $content,
            'editable' => $this->isEditable()
        ));
    }

    /**
     * Creates and returns a "default" Content for the given subject.
     *
     * @param ContentSubjectInterface $subject
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContentInterface
     */
    private function createDefaultContent(ContentSubjectInterface $subject)
    {
        $block = $this->createDefaultBlock($this->config['default_block_type']);

        $content = new Content();
        $content
            ->setVersion(1)
            ->addBlock($block);

        $subject->addContent($content);

        $this->manager->persist($content);
        $this->manager->persist($subject);
        $this->manager->flush();

        return $content;
    }

    /**
     * Creates a default block.
     *
     * @param string $type
     * @param array $datas
     *
     * @return BlockInterface
     */
    private function createDefaultBlock($type, array $datas = array())
    {
        $plugin = $this->pluginRegistry->get($type);

        return $plugin->create($datas);
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
        $token = sprintf('cms_block_%s', $block->getType());
        if (!$this->template->hasBlock($token)) {
            throw new \RuntimeException('Unable to find "%s" twig block.', $token);
        }

        return trim($this->template->renderBlock($token, array('block' => $block)));
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
        if (null === $type) {
            $type = $this->config['default_block_type'];
        }

        $repository = $this->manager->getRepository('Ekyna\Bundle\CmsBundle\Entity\AbstractBlock');
        if (null !== $block = $repository->findOneBy(array('name' => $name, 'content' => null))) {
            // TODO test block type ?
        } else {
            $block = $this->createDefaultBlock($type, $datas);
            $block->setName($name);

            $this->manager->persist($block);
            $this->manager->flush();
        }

        return $this->template->renderBlock('cms_block', array(
            'block' => $block,
            'editable' => $this->isEditable()
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
